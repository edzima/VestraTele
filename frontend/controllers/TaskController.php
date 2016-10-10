<?php

namespace frontend\controllers;

use Yii;
use common\models\Task;
use common\models\TaskSearch;
use common\models\Wojewodztwa;
use common\models\AccidentTyp;
use common\models\User;
use common\models\Powiat;
use common\models\Gmina;
use common\models\City;

use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    /**
     * @inheritdoc
     */
	 const WORK_AGENT = 2;
	 const WORK_TELE = 1;
	
	 
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
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Task model.
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
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
       $model = new Task();
	   $woj = ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name');
	   $accident = ArrayHelper::map(AccidentTyp::find()->all(),'id', 'name');
	   $agent = ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'woj' => $woj,
				'accident' => $accident,
				'agent' => $agent,
            ]);
        }
    }
	
	
	public function actionPowiat() {
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$parents = $_POST['depdrop_parents'];
			if ($parents != null) {
				$cat_id = $parents[0];
				$out = Powiat::getPowiatListId($cat_id);
				echo Json::encode(['output'=>$out, 'selected'=>'']);
				return;
			}
		}
		echo Json::encode(['output'=>'', 'selected'=>'']);
	}
	
	
	public function actionGmina() {
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$ids = $_POST['depdrop_parents'];
			$cat_id = empty($ids[0]) ? null : $ids[0];
			$subcat_id = empty($ids[1]) ? null : $ids[1];
			if ($cat_id != null && is_numeric($subcat_id)) {
			   $data = Gmina::getGminaList($cat_id,$subcat_id);
				echo Json::encode(['output'=>$data['out'], 'selected'=>$data['selected']]);
			   return;
			}
		}
		echo Json::encode(['output'=>'', 'selected'=>'']);

	}
	
	public function actionCity() {
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$ids = $_POST['depdrop_parents'];
			$cat_id = empty($ids[0]) ? null : $ids[0];
			$subcat_id = empty($ids[1]) ? null : $ids[1];
			if ($cat_id != null && is_numeric($subcat_id)) {
			   $data = City::getCitiesList($cat_id,$subcat_id);
				echo Json::encode(['output'=>$data['out'], 'selected'=>$data['selected']]);
			   return;
			}
		}
		echo Json::encode(['output'=>'', 'selected'=>'']);

	}
	
	public function actionTest(){
		
		 $data = City::getCitiesList(2,4);
		echo Json::encode(['output'=>$data['out'], 'selected'=>$data['selected']]);
	}

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$woj = ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name');
		$accident = ArrayHelper::map(AccidentTyp::find()->all(),'id', 'name');
		$agent = ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username');
        
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'woj' => $woj,
				'accident' => $accident,
				'agent' => $agent,
            ]);
        }
    }

    /**
     * Deletes an existing Task model.
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
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
