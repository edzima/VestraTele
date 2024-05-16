<?php

use common\helpers\Url;
use common\models\issue\IssueType;
use common\models\issue\Summon;
use common\modules\calendar\CalendarAsset;
use common\modules\calendar\models\searches\ContactorSummonCalendarSearch;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $users string[]|null */
/* @var $user_id int|null */
/* @var $parentType IssueType|null */
/* @var $indexUrl string */
/* @var $searchModel ContactorSummonCalendarSearch */

$this->title = Yii::t('issue', 'Calendar - Summons');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => $indexUrl];
$this->params['breadcrumbs'][] = $this->title;
$this->params['issueParentTypeNav'] = [
	'route' => ['/calendar/summon-calendar/index', 'userId' => $user_id],
];

if ($searchModel->getIssueMainType()) {
	$this->params['breadcrumbs'][] = [
		'label' => $searchModel->getIssueMainType()->name,
	];
}

CalendarAsset::register($this);
$filterGroups = [];
$statuses = $searchModel->getStatusFiltersOptions();
if (!empty($statuses)) {
	$filterGroups[] = [
		'id' => 0,
		'title' => 'Statusy',
		'filteredPropertyName' => 'statusId',
		'filters' => $statuses,
	];
}
$types = $searchModel->getTypesFilterOptions();
if (!empty($types)) {
	$filterGroups[] = [
		'id' => 1,
		'title' => 'Typy',
		'filteredPropertyName' => 'typeId',
		'filters' => $types,
	];
}
$filterGroups[] = [
	//'id' => 2,
	'title' => 'Rodzaj',
	'filteredPropertyName' => 'is',
	'filters' => $searchModel->getKindFilterOptions(),
];
$httpParams = [
	[
		'name' => 'userId',
		'value' => $user_id,
	],
];
if ($searchModel->getIssueMainType()) {
	$httpParams[] =
		[
			'name' => Url::PARAM_ISSUE_PARENT_TYPE,
			'value' => $searchModel->getIssueMainType()->id,
		];
}
$props = [
	'filterGroups' => $filterGroups,
	'eventSourcesConfig' => [
		[
			'id' => 0,
			'url' => Url::to(['summon-calendar/list']),
			'allDayDefault' => false,
			'urlUpdate' => Url::to(['summon-calendar/update']),
		],
		[
			'id' => 1,
			'url' => Url::to(['summon-calendar/deadline']),
			'allDayDefault' => true,
			'urlUpdate' => '',
			'editable' => false,
		],
		[
			'id' => 2,
			'url' => Url::to(['summon-calendar/reminder']),
			'allDayDefault' => true,
			'urlUpdate' => '',
			'editable' => false,
		],
	],
	'notesEnabled' => true,
	'extraHTTPParams' => $httpParams,
	'URLAddEvent' => Url::to('/summon/create'),
	'URLGetNotes' => Url::to(['summon-calendar-note/list']),
	'URLCreateNote' => Url::to(['summon-calendar-note/create']),
	'URLUpdateNote' => Url::to(['summon-calendar-note/update']),
	'URLDeleteNote' => Url::to(['summon-calendar-note/delete']),
];
?>
<div class="meet-calendar-calendar">

	<?= !empty($users)
		? Select2::widget([
			'data' => $users,
			'name' => 'user_id',
			'value' => $user_id,
			'pluginOptions' => [
				'placeholder' => Summon::instance()->getAttributeLabel('contractor_id'),
			],
			'pluginEvents' => [
				'change' => new JsExpression('function(event){ window.location.replace("'
					. 'index?' . Url::PARAM_ISSUE_PARENT_TYPE . "=$searchModel->issueParentTypeId"
					. '&userId='
					. '" + this.value);}'),
			],
		])
		: '' ?>

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
