<?php

use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Calculations min counts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Calculations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-calculation-min-count-list">

	<h1><?= Html::encode($this->title) ?></h1>


	<p>
		<?= Html::a(Yii::t('backend', 'Set min count'),
			['set'],
			[
				'class' => 'btn btn-success',
			]) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'class' => ActionColumn::class,
				'template' => '{update}',
			],
			[
				'attribute' => 'type_id',
				'value' => 'type.name',
			],
			[
				'attribute' => 'stage_id',
				'value' => 'stage.name',
			],
			[
				'attribute' => 'min_calculation_count',
			],
		],
	]) ?>


</div>
