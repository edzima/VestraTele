<?php

namespace frontend\controllers;

use Yii;
use common\models\Task;
use common\models\TaskStatusSearch;
use common\models\TaskStatus;
use common\models\TaskExtra;
use common\models\AnswerTyp;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
/**
 * TaskStatusController implements the CRUD actions for TaskStatus model.
 */
class TaskStatusController extends Controller
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
     * Lists all TaskStatus models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskStatusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		$answers = ArrayHelper::map(AnswerTyp::find()->all(),'id', 'name');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'answers' =>$answers,
        ]);
    }

    /**
     * Displays a single TaskStatus model.
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
     * Creates a new TaskStatus model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRaport($id)
	{
		if(Yii::$app->user->identity->isTele()) throw new NotFoundHttpException('Brak uprawnień ;)');
		if (($model = TaskStatus::findOne($id)) == null) $model = new TaskStatus();
		$model->task_id = $id;
		$task = $this->findTask($id);
		
		$answers = ArrayHelper::map(AnswerTyp::find()->all(),'id', 'name');

        if ($model->load(Yii::$app->request->post()) && $model->save() ) {
            return $this->redirect(['view', 'id' => $model->task_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'task' =>$task,
				'answers' =>$answers,
				
            ]);
        }
    }

    /**
     * Finds the TaskStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaskStatus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskStatus::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	 protected function findTask($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
