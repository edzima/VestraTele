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
     * param $key - selected item
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
	{	//tele dodac komentarz , reszta wylaczona ale widoczna
		if(Yii::$app->user->identity->isTele()) throw new NotFoundHttpException('Brak uprawnień ;)');

		if (($model = TaskStatus::findOne($id)) == null) $model = new TaskStatus();
		$model->task_id = $id;
		$task = $this->findTask($id);

		$answers = ArrayHelper::map(AnswerTyp::find()->all(),'id', 'name');

		//task-status save
        if($model->load(Yii::$app->request->post())) {
			//umowa == finished
			if($model->answer_id==1) $model->finished = 1;
			$model->save();
			return $this->redirect(['view', 'id' => $model->task_id]);
		}

		//task date save
		if($task->load(Yii::$app->request->post()) && $task->save())	return $this->redirect(['view', 'id' => $model->task_id]);


		else {
            return $this->render('create', [
                'model' => $model,
				'task' =>$task,
				'answers' =>$answers,

            ]);
        }
    }



	 public function actionTeleview($id)
	{	//tele dodac komentarz , reszta wylaczona ale widoczna

		$model = $this->findModel($id);
		$task = $this->findTask($id);

        if ($model->load(Yii::$app->request->post()) && $model->save() ) {
            return $this->redirect(['view', 'id' => $model->task_id]);
        } else {
            return $this->render('teleraport', [
                'model' => $model,
				'task' =>$task,
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
            throw new NotFoundHttpException('Spotkania jeszcze nie zaraportowano.');
        }
    }

	 protected function findTask($id)
    {
        $model = Task::find()
            ->where([
                'id' => $id,
                'agent_id' => Yii::$app->user->identity->id
            ])
            ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Nie ma takiego spotkania.');
        }
    }
}
