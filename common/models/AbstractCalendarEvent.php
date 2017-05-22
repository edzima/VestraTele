<?php
namespace common\models;
use common\models\Task;
use common\models\CalendarNews;
use yii;
//use \path\to\some\other\model\to\use\OtherModels;

abstract class AbstractCalendarEvent {

    protected $id;
    protected $url;
    protected $title;
    protected $description;
    protected $start;
    protected $end;
    protected $textColor;
    protected $color;
    protected $borderColor;

    abstract public function toArray();

    protected function setUrl(){
        $this->url = $this->url.$this->id;
    }


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

    public function getUrl(){
        return $this->url;
    }


}
