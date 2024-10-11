<?php

use common\helpers\Html;
use common\modules\calendar\CalendarAsset;
use common\modules\calendar\models\searches\LawsuitCalendarSearch;
use common\modules\calendar\models\searches\LawsuitSummonCalendarSearch;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $summonsModel LawsuitSummonCalendarSearch */

$this->title = Yii::t('court', 'Calendar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['/court/lawsuit/index']];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);

$filterGroups = [];
$filterGroups[] = [
	'id' => 0,
	'title' => Yii::t('court', 'Courts'),
	'filteredPropertyName' => 'courtType',
	'filters' => LawsuitCalendarSearch::getCourtFilters(),
];
$filterGroups[] = [
	'id' => 1,
	'title' => Yii::t('court', 'Is Appeal'),
	'filteredPropertyName' => 'is_appeal',
	'filters' => LawsuitCalendarSearch::getIsAppealFilters(),
];
$summonsTypesFilter = $summonsModel->getSummonsTypesFilters();
if (!empty($summonsTypesFilter)) {
	$filterGroups[] = [
		'id' => 2,
		'title' => Yii::t('court', 'Summons'),
		'filteredPropertyName' => 'typeId',
		'filters' => $summonsTypesFilter,
	];
}

$eventsSources = [];
$eventsSources[] = [
	'id' => 0,
	'url' => Url::to(['list']),
	'allDayDefault' => false,
	'urlUpdate' => Url::to(['update']),
];
if (!empty($summonsTypesFilter)) {
	$eventsSources[] = [
		'id' => 1,
		'url' => Url::to(['summons-list']),
		'allDayDefault' => true,
		'urlUpdate' => '',
		'editable' => false,
	];
}

$props = [
	'filterGroups' => $filterGroups,
	'eventSourcesConfig' => $eventsSources,
	'allowUpdate' => false,
	'notesEnabled' => true,
	'URLGetNotes' => Url::to(['lawsuit-calendar-note/list']),
	'URLCreateNote' => Url::to(['lawsuit-calendar-note/create']),
	'URLUpdateNote' => Url::to(['lawsuit-calendar-note/update']),
	'URLDeleteNote' => Url::to(['lawsuit-calendar-note/delete']),
];
?>
<div class="lawsuit-calendar">

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
