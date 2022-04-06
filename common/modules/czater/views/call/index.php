<?php

use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ArrayDataProvider */

$this->title = Yii::t('czater', 'Calls');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		'clientFullNumber',
		'clientName',
		'duration',
		'status',
		'dateRequested:datetime',
		'consultantName',
		'consultantNumber',
		'dateStart:datetime',
		'dateFinish:datetime',
		[
			'class' => ActionColumn::class,
			'template' => '{view}',
		],
	],
]) ?>
