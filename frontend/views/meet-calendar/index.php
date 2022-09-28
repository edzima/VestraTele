<?php

use common\models\CalendarNews;
use common\modules\calendar\CalendarAsset;
use frontend\models\AgentMeetCalendarSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $agents string[] */
/* @var $extraParams string[] */

$this->title = 'Kalendarz';
$this->params['breadcrumbs'][] = ['label' => 'Leady', 'url' => ['/meet/index']];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);
$agentId = $extraParams[0]['value'];

$props = [
	'extraHTTPParams' => $extraParams,
	'filterGroups' => [
		[
			'id' => 0,
			'title' => 'Statusy',
			'filteredPropertyName' => 'statusId',
			'filters' => AgentMeetCalendarSearch::getFiltersOptions(),
		],
		[
			'id' => 1,
			'title' => 'Notatki',
			'filteredPropertyName' => 'isNote',
			'filters' => CalendarNews::getFilters(),
		],
	],
	'eventSourcesConfig' => [
		[
			'id' => 0,
			'url' => '/meet-calendar/list',
			'allDayDefault' => false,
			'urlUpdate' => '/meet-calendar/update'
		],
	],

	'URLAddEvent' => Url::to('/meet/create'),

	'URLGetNotes' => Url::to('/calendar-note/list'),
	'URLNewNote' => Url::to('/calendar-note/add'),
	'URLUpdateNote' => Url::to('/calendar-note/update'),
	'URLDeleteNote' => Url::to('/calendar-note/delete'),

];
?>
<div class="meet-calendar-calendar">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= empty($agents)
		? ''
		: Html::dropDownList('agentId', $agentId, $agents, [
			'onChange' => 'window.location.replace("' . Url::to('/meet-calendar/index?agentId=') . '" + this.value);',
		]) ?>

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
