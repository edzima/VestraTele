<?php

namespace frontend\controllers;

use Yii;
use common\models\Score;
use common\models\Connexion;
use common\models\ScoreSearch;
use common\models\User;
use common\models\Task;
use common\models\TaskStatus;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

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
		if (!$count) throw new NotFoundHttpException('Brak podpisanych umów w tym spotkaniu');
        
		$scores = Score::find()->where(['task_id' => $id, 'tele_id' => $teleID])->all();
		$connexion = ArrayHelper::map(Connexion::find()->all(),'id', 'name');
		$tele = ArrayHelper::map(User::find()->where(['typ_work' => 'T'])->all(), 'id', 'username');
		
		$task = Task::findOne($id);
		//new agreement after update (extra_agreement)
		if(count($scores)<$count){
			$odds = $count - count($scores);
			//Create an array of the score submitted
			for($i = 1; $i <=$odds; $i++) {
				$scores[] = new Score();
			}
		}
		
	
		return $this->render('_dealForm', [
			'scores' => $scores,
			'connexion' => $connexion,
			'tele' => $tele,
			'task' => $task
			]);
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
