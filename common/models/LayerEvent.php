<?php
namespace common\models;
use common\models\AbstractCalendarEvent;
use yii;
//use \path\to\some\other\model\to\use\OtherModels;

class LayerEvent extends AbstractCalendarEvent{

    protected $url = '/cause/update?id=';
    private $isExpired = false;
    private $period;


    public function toArray(){
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

        ];

        if($this->isExpired) $event['isExpired'] = $this->isExpired;


        return $event;
    }

    public function generateNextStep(){

        $start = new \DateTime($this->start.' +'.$this->period.'day');
        $now = new \DateTime();
        $this->start =  Yii::$app->formatter->asDate($start, 'yyyy-MM-dd HH:mm');
        $this->isExpired = $now > $start;

        if($this->isExpired){
            $this->color = "red";
        }


    }

    public function __construct(Cause $cause) {
        $this->title = $cause->victim_name;
        $this->id = $cause->id;
        $this->setUrl($cause->id);
        $category = $cause->category;
        $this->period = $category->period;
        $this->description = $category->name;
        $this->color = $category->color;
        $this->start =    Yii::$app->formatter->asDate($cause->date, 'yyyy-MM-dd HH:mm');
    }


}
