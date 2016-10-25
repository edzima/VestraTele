<?php

namespace backend\controllers;

use Yii;
use common\models\Score;
use common\models\Connexion;
use backend\models\search\ScoreSearch;
use common\models\User;
use common\models\Task;
use common\models\TaskStatus;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * ScoreController implements the CRUD actions for Score model.
 */
class ScoreController extends Controller
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
     * Lists all Score models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ScoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Score model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
	
	
	public function actionDeal()
    {	
		$id = Yii::$app->request->queryParams['1']["id"];
		$teleID = Yii::$app->request->queryParams['1']["tele"];
		
		//check task with tele  and count_agreement
		$count = @TaskStatus::findOne($id)->count_agreement;
		if (($model = Task::findOne(['id'=>$id,'tele_id'=>$teleID])) == null) throw new NotFoundHttpException('Nie ma takiego spotkania do rodzielenia.');
		if (!$count) throw new NotFoundHttpException('Brak podpisanych umów do rozdzielenia');
        
		$scores = Score::find()->where(['task_id' => $id, 'tele_id' => $teleID])->all();
		$connexion = ArrayHelper::map(Connexion::find()->all(),'id', 'name');
		$tele = ArrayHelper::map(User::find()->where(['typ_work' => 'T'])->all(), 'id', 'username');
		$task = Task::findOne($id);
		//new agreement after update
		
		if(count($scores)<$count){
			$odds = $count - count($scores);
			//Create an array of the score submitted
			for($i = 1; $i <=$odds; $i++) {
				$scores[] = new Score();
			}
		}
		
		//set task_id and tele 
		foreach ($scores as $score) {
			$score->task_id = $id;
			$score->tele_id = $teleID;
		}
		

		//Load and validate the multiple models
		if (Score::loadMultiple($scores, Yii::$app->request->post()) && Score::validateMultiple($scores)) {
		
			foreach ($scores as $score) {
				//Try to save the models. Validation is not needed as it's already been done.
				$score->save(false);
			}
			
			//granted boolean point to taskStatus
			$taskS= TaskStatus::findOne($id);
			$taskS->point=1;
			$taskS->save();
			
			return $this->redirect('index');
		}

		return $this->render('_dealForm', [
			'scores' => $scores,
			'connexion' => $connexion,
			'tele' => $tele,
			'task' => $task,
			]);
    }


    /**
     * Creates a new Score model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	
        $model = new Score();
		$connexion = ArrayHelper::map(Connexion::find()->all(),'id', 'name');
		$tele = ArrayHelper::map(User::find()->where(['typ_work' => 'T'])->all(), 'id', 'username');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'connexion' => $connexion,
				'tele' => $tele,
            ]);
        }
    }

    /**
     * Updates an existing Score model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {	
		$count = 2;
        $model = $this->findModels(13,3);
		$connexion = ArrayHelper::map(Connexion::find()->all(),'id', 'name');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'connexion' => $connexion,
            ]);
        }
    }
	
	


    /**
     * Deletes an existing Score model.
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
     * Finds the Score model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Score the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Score::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
