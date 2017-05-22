<?php

namespace frontend\controllers;

use common\models\CauseCategory;
use Yii;
use common\models\Cause;
use common\models\CauseSearch;
use common\models\CalendarNews;
use common\models\LayerEvent;
use common\models\NewsEvent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;


/**
 * CauseController implements the CRUD actions for Cause model.
 */
class CauseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Lists all Cause models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CauseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCalendar(){

        return $this->render('calendar');

    }

    /**
     * Displays a single Cause model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Cause model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Cause();
        $model->author_id = Yii::$app->user->id;
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'nameWithPeriod');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'category' => $category
            ]);
        }
    }

    public function actionCreateAjax() {
        //
        $model = new Cause();
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'nameWithPeriod');
        $model->author_id = Yii::$app->user->identity->getId();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = [];
            $data['status']= true;
            $data['url'] = "/cause/update-ajax?id=".$model->id;
            $data['id'] = $model->id;
            return $data;

            //return $this->redirect(['view', 'id' =>  $model->id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_createForm', [
                'model' => $model,
                'category' => $category
            ]);
        } else {
            return $this->render('_createForm', [
                'model' => $model,
                'category' => $category
            ]);
        }
    }




    /**
     * Updates an existing Cause model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'nameWithPeriod');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'category' => $category
            ]);
        }
    }


    /**
     * @param integer $id
     * @return string|\yii\web\Response
     */
    public function actionUpdateAjax($id) {

        $model = $this->findModel($id);
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'nameWithPeriod');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data['status']= true;
            return $data;
            //return $this->redirect(['view', 'id' =>  $model->id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_updateForm', [
                'model' => $model,
                'category' => $category
            ]);
        } else {
            return $this->render('_updateForm', [
                'model' => $model,
                'category' => $category
            ]);
        }
    }

    public function actionTest()
    {
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'nameWithPeriod');
        print_r($category);
    }


    public function actionLayerEvents( $start, $end)
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $start = strtotime($start);
        $end = strtotime($end);
        $model = Cause::find()
            ->where([
                'author_id' => Yii::$app->user->identity->id,
                'is_finished' => 0,
            ])
            //->andWhere("date BETWEEN '$start' AND '$end'")
            ->all();

        $events = [];
        foreach ($model as $cause) {
            $event = new LayerEvent($cause);
            $events[] = $event->toArray();
            $event->generateNextStep();
            $events[] = $event->toArray();

        }

        return $events;
    }

    public function actionLayerNews( $start, $end)
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = CalendarNews::find()
            ->where(['agent_id' => Yii::$app->user->identity->id])
            ->andWhere("start BETWEEN '$start' AND '$end'")
            ->all();
        $events = [];
        foreach ($model as $calendarNews) {
            $event = new NewsEvent($calendarNews);
            $events[] = $event->toArray();
        }
        return $events;

    }



    /**
     * Deletes an existing Cause model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Cause model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cause the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cause::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
