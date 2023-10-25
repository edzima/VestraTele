<?php

use common\modules\calendar\CalendarAsset;
use common\modules\calendar\models\searches\IssueStageDeadlineCalendarSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $users string[]|null */
/* @var $indexUrl string */
/* @var $searchModel IssueStageDeadlineCalendarSearch */

$this->title = Yii::t('issue', 'Stages Deadlines');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => $indexUrl];
if ($searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = ['label' => $searchModel->getIssueMainType()->name];
}
$this->params['issueParentTypeNav'] = [
	'route' => ['/calendar/issue-stage-deadline/index'],
];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);
$props = [
	'filterGroups' => [
		[
			'id' => 0,
			'title' => Yii::t('issue', 'Stages'),
			'filteredPropertyName' => 'stageId',
			'filters' => $searchModel->getStagesFilters(),
		],
		[
			'id' => 1,
			'title' => Yii::t('issue', 'Lawyer'),
			'filteredPropertyName' => 'lawyerId',
			'filters' => $searchModel->getLawyersFilters(),
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
	'calendarOptions' => [
		'eventLimit' => 10,
	],
	'notesEnabled' => true,
	'URLGetNotes' => Url::to(['issue-stage-deadline-calendar-note/list']),
	'URLCreateNote' => Url::to(['issue-stage-deadline-calendar-note/create']),
	'URLUpdateNote' => Url::to(['issue-stage-deadline-calendar-note/update']),
	'URLDeleteNote' => Url::to(['issue-stage-deadline-calendar-note/delete']),
];
?>
<div class="meet-calendar-calendar">

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
