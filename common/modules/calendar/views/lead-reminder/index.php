<?php

use common\helpers\Html;
use common\modules\calendar\CalendarAsset;
use common\modules\lead\models\searches\LeadReminderSearch;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $users string[]|null */
/* @var $indexUrl string */

$this->title = Yii::t('lead', 'Calendar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reminders'), 'url' => ['/lead/reminder/index']];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);

$props = [
	'filterGroups' => [
		[
			'id' => 3,
			'title' => " ",
			'filteredPropertyName' => 'isStatusDeadline',
			'filters' => LeadReminderSearch::getStatusDeadlineFilters(),
		],
		[
			'id' => 0,
			'title' => Yii::t('lead', 'Statuses'),
			'filteredPropertyName' => 'statusId',
			'filters' => LeadReminderSearch::getStatusesFilters(),
		],
		[
			'id' => 1,
			'title' => Yii::t('common', 'Priority'),
			'filteredPropertyName' => 'priority',
			'filters' => LeadReminderSearch::getPriorityFilters(),
		],
		[
			'id' => 2,
			'title' => Yii::t('lead', 'Is Done'),
			'filteredPropertyName' => 'isDone',
			'filters' => LeadReminderSearch::getIsDoneFilters(),
		],

	],
	'eventSourcesConfig' => [
		[
			'id' => 0,
			'url' => Url::to(['list']),
			'allDayDefault' => false,
			'urlUpdate' => Url::to(['update']),
		],
		[
			'id' => 1,
			'url' => Url::to(['status-deadline']),
			'allDayDefault' => false,
		],
	],
	'notesEnabled' => true,
	'URLAddEvent' => Url::to(['/lead/lead/create']),
	'URLGetNotes' => Url::to(['lead-calendar-note/list']),
	'URLCreateNote' => Url::to(['lead-calendar-note/create']),
	'URLUpdateNote' => Url::to(['lead-calendar-note/update']),
	'URLDeleteNote' => Url::to(['lead-calendar-note/delete']),
];
?>
<div class="meet-calendar-calendar">

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
