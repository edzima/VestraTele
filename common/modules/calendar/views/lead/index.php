<?php

use common\modules\calendar\assets\Asset;
use frontend\models\AgentMeetCalendarSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $userId int */
/* @var $filters array */

$this->title = Yii::t('lead', 'Calendar - Leads');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = $this->title;

Asset::register($this);

$props = [
	'agentId' => $userId,
	'filtersItems' => $filters,
	'URLGetEvents' => Url::to('list'),
	'URLUpdateEvent' => Url::to('update'),

	'URLAddEvent' => Url::to('/lead/lead/create'),

	'URLGetNotes' => Url::to('note/list'),
	'URLNewNote' => Url::to('note/add'),
	'URLUpdateNote' => Url::to('note/update'),
	'URLDeleteNote' => Url::to('note/delete'),

];

var_dump($filters);
var_dump(AgentMeetCalendarSearch::getFiltersOptions());
$props = [
	'agentId' => $userId,
	'filtersItems' => AgentMeetCalendarSearch::getFiltersOptions(),

	'URLGetEvents' => Url::to('list'),
	'URLUpdateEvent' => Url::to('/meet-calendar/update'),

	'URLAddEvent' => Url::to('/meet/create'),

	'URLGetNotes' => Url::to('/calendar-note/list'),
	'URLNewNote' => Url::to('/calendar-note/add'),
	'URLUpdateNote' => Url::to('/calendar-note/update'),
	'URLDeleteNote' => Url::to('/calendar-note/delete'),

];
?>
<div class="calendar-lead-calendar">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
