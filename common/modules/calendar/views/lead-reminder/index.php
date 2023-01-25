<?php

use common\modules\calendar\CalendarAsset;
use common\modules\lead\models\searches\LeadReminderSearch;
use yii\helpers\Html;
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
			'id' => 0,
			'title' => 'Statusy',
			'filteredPropertyName' => 'statusId',
			'filters' => LeadReminderSearch::getStatusesFilters(),
		],
		[
			'id' => 1,
			'title' => 'Priorytet',
			'filteredPropertyName' => 'priority',
			'filters' => LeadReminderSearch::getPriorityFilters(),
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
