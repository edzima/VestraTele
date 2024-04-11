<?php

use common\helpers\Html;
use common\modules\calendar\CalendarAsset;
use common\modules\calendar\models\searches\LawsuitCalendarSearch;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = Yii::t('court', 'Calendar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Lawsuits'), 'url' => ['/court/lawsuit/index']];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);

$props = [
	'filterGroups' => [
		[
			'id' => 0,
			'title' => Yii::t('court', 'Courts'),
			'filteredPropertyName' => 'courtType',
			'filters' => LawsuitCalendarSearch::getCourtFilters(),
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
