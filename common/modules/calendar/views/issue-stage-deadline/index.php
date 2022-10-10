<?php

use common\modules\calendar\CalendarAsset;
use common\modules\calendar\models\searches\IssueStageDeadlineCalendarSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $users string[]|null */
/* @var $indexUrl string */

$this->title = Yii::t('issue', 'Calendar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => $indexUrl];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);
$props = [
	'filterGroups' => [
		[
			'id' => 0,
			'title' => Yii::t('issue', 'Stages'),
			'filteredPropertyName' => 'stageId',
			'filters' => IssueStageDeadlineCalendarSearch::getStagesFilters(),
		],
		[
			'id' => 1,
			'title' => Yii::t('issue', 'Lawyer'),
			'filteredPropertyName' => 'lawyerId',
			'filters' => IssueStageDeadlineCalendarSearch::getLawyersFilters(),
		],
	],
	'eventSourcesConfig' => [
		[
			'id' => 0,
			'url' => Url::to(['list']),
			'allDayDefault' => false,
			'urlUpdate' => Url::to(['update']),
		],
	],
	'notesEnabled' => false,
	'calendarOptions' => [
		'eventLimit' => 10,
	],
];
?>
<div class="meet-calendar-calendar">

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
