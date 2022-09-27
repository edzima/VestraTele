<?php

use common\models\issue\Summon;
use frontend\assets\CalendarAsset;
use frontend\models\ContactorSummonCalendarSearch;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $users string[]|null */
/* @var $user_id int|null */

$this->title = Yii::t('issue', 'Calendar');
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
	'extraHTTPParams' => [
		[
			'name' => 'userId',
			'value' => $user_id,
		],
	],
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
				'change' => new JsExpression('function(event){ window.location.replace("' . Url::to('/summon-calendar/index?userId=') . '" + this.value);}'),
			],
		])
		: '' ?>

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
