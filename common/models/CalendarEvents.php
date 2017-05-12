<?php
namespace common\models;
use common\models\Task;
use common\models\CalendarNews;
use yii;
//use \path\to\some\other\model\to\use\OtherModels;

class CalendarEvents extends \yii\base\Object{

    private $id;
    private $url = '';
    private $title = '';
    private $description ='';
    private $start;
    private $end;
    private $color = "";
    private $textColor ='';
    private $borderColor;
    private $allDay = false;
    private $isNews = false;
    private $task;


    public function getTitle(){
        return $this->title;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getColor(){
        return $this->color;
    }

    public function getTextColor(){
        return $this->textColor;
    }

    public function getBorderColor(){
        return $this->borderColor;
    }

    public function getUrlUpdate(){

        if(Yii::$app->user->can('manager') || $this->task->tele_id==Yii::$app->user->identity->id ) {
            $this->url =  '/spotkanie/edycja?id='.$this->id;
        }
        return $this->url;
    }

    public function getUrlRaport(){
        $this->url = '/task-status/raport?id='.$this->id;
        return $this->url;
    }

    public function getUrlCause(){
        $this->url = '/cause/update?id='.$this->id;
        return $this->url;

    }

    public function setPeriod($period){

        $start = new \DateTime($this->start);
        $withPeriod = new \DateTime($this->start.' +'.$period.'day');
        $now = new \DateTime();
        $now->sub($start);



        $this->start =   Yii::$app->formatter->asDate($withPeriod, 'yyyy-MM-dd HH:mm');
        $this->end = $now;
    }

    public function getToArray(){
        $event = [
          'id' => $this->id,
          'title' => $this->title,
          'start' => $this->start,
          'end' => $this->end,
          'description' => $this->description,
          'borderColor' => $this->borderColor,
          'color' => $this->color,
          'url' => $this->url,
          'textColor' => $this->textColor,
          'allDay' => $this->allDay,
          'isNews' => $this->isNews
        ];
        return $event;
    }

    private function EventColor($task){
        $textColor = "white";
        $color = "green";
        $borderColor = '';
        if($task->meeting) $color = "blue";
        if($task->automat) $color = "red";
        $answer = $task->taskstatus->answer;
        if($answer) {
            $textColor = $color;
            $color = "#f5f5f5";
            if($answer->name=="umowa + EKSTRA") $color = "yellow";
        }
        if($task->tele_id==Yii::$app->user->identity->id) $borderColor = "#00ffff";
        $colors = [
            "color" => $color,
            "textColor" => $textColor,
            "borderColor" => $borderColor,
        ];
        $this->color = $color;
        $this->textColor = $textColor;
        $this->borderColor = $borderColor;

    }

    public static function withCalendarNews($calendarNews){
        $instance = new Self();
        $instance->id = $calendarNews->id;
        $instance->title = $calendarNews->news;
        $instance->start = $calendarNews->start;
        $instance->end = $calendarNews->end;
        $instance->allDay = true;
        $instance->isNews = true;

        return $instance;
    }

    /**
     * @param Cause $cause
     * @return CalendarEvents
     */
    public static function withCause(Cause $cause){
        $instance = new Self();
        $instance->id = $cause->id;
        $instance->title = $cause->victim_name;
        $instance->start =    Yii::$app->formatter->asDate($cause->date, 'yyyy-MM-dd HH:mm');

        //$period = $cause->category->period;


        //$instance->end = $cause->end;
        //$instance->allDay = true;
        //$instance->isNews = true;

        return $instance;
    }

    /**
     * @param $task
     * @return CalendarEvents
     */
    public static function withTask($task){

        $instance = new Self();
        $city = $task->miasto->name;
        $powiat = $task->powiatRel->name;
        $woj = $task->wojewodztwo->name;
        $gmina = $task->gminaRel->name;

        ($gmina ? $instance->title = $gmina.', '.$city : $instance->title = $city );
        $instance->description = $powiat."<br/>".$woj;
        $instance->id = $task->id;
        $instance->task = $task;
        $instance->start = $task->date;
        $instance->EventColor($task);

        return $instance;

    }



}
