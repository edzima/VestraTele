<?php

use frontend\assets\CalendarAsset;
use common\models\issue\SummonSearch;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $agents string[] */
/* @var $agentId int */

$this->title = 'Kalendarz';
$this->params['breadcrumbs'][] = ['label' => 'Wezwania', 'url' => ['/summons/index']];
$this->params['breadcrumbs'][] = $this->title;

CalendarAsset::register($this);

$props = [
	'agentId' => $agentId,
//	'filtersItems' => SummonSearch::getFiltersOptions(),

	'URLGetEvents' => Url::to('/summon-calendar/list'),
	'URLUpdateEvent' => Url::to('/summon-calendar/update'),

	'URLAddEvent' => Url::to('/summon/create'),

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
			'onChange' => 'window.location.replace("' . Url::to('/summon-calendar/index?agentId=') . '" + this.value);',
		]) ?>

	<?= Html::tag('div', '', ['id' => 'app', 'data-props' => Json::encode($props)]) ?>

</div>
