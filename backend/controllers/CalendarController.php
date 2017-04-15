<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\CalendarNews;
use common\models\Task;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class CalendarController extends Controller{

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
                    'addnews' => ['POST'],
                ],
            ],
        ];
    }

    public function actionView($id){

      $agent = ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username');
      return $this->render('view', ['agent' => $agent,'id' => $id]);
    }

    // $id -> agent_id news
    public function actionAddnews(){
        $agent=Yii::$app->request->post()['agent'];
        $start = Yii::$app->request->post()['start'];
        $end =  Yii::$app->request->post()['end'];
        $newsText = Yii::$app->request->post()['newsText'];

         $model = new Calendarnews();
         $model->agent_id = $agent;
         $model->news = $newsText;
         $model->start = $start;
         $model->end = $end;
         $model->save();
         echo $model->getPrimaryKey();

    }


    public function actionAgenttask($id){
      //->where(['agent_id' => $id])
     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


     if($id){
         $task = Task::find()->where(['agent_id' => $id])->all();
     }
     else{
          $task = Task::find()->all();
     }
      $events = array();
      foreach ($task as $key ) {
        $event = [
          'id' => $key['id'],
          'title' => $key['victim_name'],
          'start' => $key['date'],
         // 'content' => $key['phone'],
          //'url' => '/spotkanie/edycja?id='.$key['id']
        ];
        $events[] = $event;

      }
      //echo Json::encode($events);
      return $events;
    }

    public function actionAgentnews($id){
      //->where(['agent_id' => $id])
     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


     if($id){
         $task = CalendarNews::find()->where(['agent_id' => $id])->all();
     }
     else {
        $task = CalendarNews::find()->all();
     }
      $events = array();
      foreach ($task as $key ) {
        $event = [
          'id' => $key['id'],
          'title' => $key['news'],
          'start' => $key['start'],
          'end' => $key['end'],
          'allDay' => 'true',
          //'url' => '/spotkanie/edycja?id='.$key['id']
        ];
        $events[] = $event;

      }
      //echo Json::encode($events);
      return $events;
    }

  public function actionRemove()
  {   $id=Yii::$app->request->post()['event_id'];
      $this->delete($id);
      echo $id;
  }

  public function actionUpdate($id,$start, $end)
  {
        $model = $this->findModel($id);
        $model->start = $start;
        $model->end = $end;
        if($model->save()) return true;
        else return false;

  }

  protected function delete($id)
  {
      $this->findModel($id)->delete();
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
      if (($model = CalendarNews::findOne($id)) !== null) {
          return $model;
      } else {
          throw new NotFoundHttpException('The requested page does not exist.');
      }
  }
}
