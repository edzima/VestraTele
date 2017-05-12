<?php

namespace frontend\controllers;

use common\models\CauseCategory;
use Yii;
use common\models\Cause;
use common\models\CauseSearch;
use common\models\CalendarEvents;
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


    public function actionPeriod(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $start = new \DateTime("2017-05-08 06:30");
        //$start->format('Y-m-d H:i:s');
        $dt = new \DateTime();


        $alert = $dt < $start;
        var_dump($alert);
        //echo $dt->format('Y-m-d H:i:s');
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

        $model = new Cause();
        $model->author_id = Yii::$app->user->id;
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'name');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('calendar', [
                'model' => $model,
                'category' => $category
            ]);
        }


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
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'name');

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
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'name');
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
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'name');
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
        $category = ArrayHelper::map(CauseCategory::find()->all(), 'id', 'name');
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



    public function actionLayerEvents( $start=null, $end=null)
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = Cause::find()
            ->where(['author_id' => Yii::$app->user->identity->id])
            //->andWhere("start BETWEEN '$start' AND '$end'")
            ->all();


        $events = [];
        foreach ($model as $cause) {
            $event = CalendarEvents::withCause($cause);
            //set url to update cause
            $event->getUrlCause();
            $events[] = $event->toArray;

            // finish stage
            $event->setPeriod($cause->category->period);
            $events[] = $event->toArray;


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
