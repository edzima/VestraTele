<?php

use frontend\assets\CalendarAsset;
use frontend\models\ContactorSummonCalendarSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Kalendarz';
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Summons'), 'url' => ['/summon/index']];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);

$props = [
	'filterGroups' => [
		[
			'id' => 0,
			'title' => 'Statusy',
			'filteredPropertyName' => 'statusId',
			'filters' => ContactorSummonCalendarSearch::getStatusFiltersOptions(),
		],
		[
			'id' => 1,
			'title' => 'Typy',
			'filteredPropertyName' => 'typeId',
			'filters' => ContactorSummonCalendarSearch::getTypesFilterOptions(),
		],
		[
			'id' => 2,
			'title' => 'Rodzaj',
			'filteredPropertyName' => 'is',
			'filters' => ContactorSummonCalendarSearch::getKindFilterOptions(),
		],
	],
	'eventSourcesConfig' => [
		[
			'id' => 0,
			'url' => '/summon-calendar/list',
			'allDayDefault' => false,
			'urlUpdate' => '/summon-calendar/update',
		],
		[
			'id' => 1,
			'url' => '/summon-calendar/deadline',
			'allDayDefault' => false,
			'urlUpdate' => '',
			'editable' => false,
		],
	],

	'URLAddEvent' => Url::to('/summon/create'),
	'notesEnabled' => true,
];
?>
<div class="meet-calendar-calendar">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
