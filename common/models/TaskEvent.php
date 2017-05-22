<?php
namespace common\models;
/**
 * Created by PhpStorm.
 * User: edzi
 * Date: 2017-05-22
 * Time: 13:19
 */
use yii;
use common\models\AbstractCalendarEvent;


class TaskEvent extends  AbstractCalendarEvent
{
    protected $isNews = false;
    protected $allDay = false;

    public function updateURL(){
        if(Yii::$app->user->can('manager') || $this->task->tele_id==Yii::$app->user->identity->id ) {
            $this->url =  '/spotkanie/edycja?id='.$this->id;
        }
    }

    public function raportURL(){
        $this->url = '/task-status/raport?id='.$this->id;

    }

    public function backendRaportURL(){
        $this->url = '/backend/web/task-status/raport?id='.$this->id;
    }

    public function toArray()
    {
        $event =  $event = [
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


    public function __construct(Task $task) {
        $this->id = $task->id;
        $city = $task->miasto->name;
        $powiat = $task->powiatRel->name;
        $woj = $task->wojewodztwo->name;
        $gmina = @$task->gminaRel->name;
        if($gmina)  $this->title = $gmina.', '.$city;
        else $this->title = $city;
        $this->description = $powiat."<br/>".$woj;
        $this->start = $task->date;
        $this->EventColor($task);

    }

    private function EventColor($task){
        $textColor = "white";
        $color = "green";
        $borderColor = '';
        if($task->meeting) $color = "blue";
        if($task->automat) $color = "red";
        $answer = @$task->taskstatus->answer;
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



}