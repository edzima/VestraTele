<?php
namespace common\models;
/**
 * Created by PhpStorm.
 * User: edzi
 * Date: 2017-05-22
 * Time: 13:19
 */
use yii;
use common\models\TaskEvent;


class NewsEvent extends  TaskEvent
{
    public function __construct(CalendarNews $calendarNews) {

        $this->id = $calendarNews->id;
        $this->title = $calendarNews->news;
        $this->start = $calendarNews->start;
        $this->end = $calendarNews->end;
        $this->allDay = true;
        $this->isNews = true;

    }

}