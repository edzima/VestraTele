<?php

namespace frontend\controllers;

use Yii;
use common\models\Powiat;
use common\models\PowiatSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * PowiatController implements the CRUD actions for Powiat model.
 */
class PowiatController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
			],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Displays a single Powiat model.
     * @param integer $id
     * @param integer $wojewodztwo_id
     * @return mixed
     */
    public function actionView($id, $wojewodztwo_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $wojewodztwo_id),
        ]);
    }

    /**
     * Creates a new Powiat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Powiat();
		
		//new Powiat ->38
		$model->id= 38;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'wojewodztwo_id' => $model->wojewodztwo_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Powiat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $wojewodztwo_id
     * @return mixed
     */
    public function actionUpdate($id, $wojewodztwo_id)
    {
        $model = $this->findModel($id, $wojewodztwo_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'wojewodztwo_id' => $model->wojewodztwo_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Powiat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $wojewodztwo_id
     * @return mixed
     */
    public function actionDelete($id, $wojewodztwo_id)
    {
        $this->findModel($id, $wojewodztwo_id)->delete();

        return $this->redirect(['create']);
    }

    /**
     * Finds the Powiat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $wojewodztwo_id
     * @return Powiat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $wojewodztwo_id)
    {
        if (($model = Powiat::findOne(['id' => $id, 'wojewodztwo_id' => $wojewodztwo_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
