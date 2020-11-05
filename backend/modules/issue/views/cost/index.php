<?php

use backend\modules\issue\models\search\IssueCostSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use kartik\grid\SerialColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueCostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Costs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-cost-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $model,
		'columns' => [
			['class' => SerialColumn::class],
			[
				'class' => IssueColumn::class,
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssueCostSearch::getTypesNames(),
			],
			[
				'attribute' => 'value',
				'value' => 'valueWithVAT',
				'format' => 'currency',
				'label' => Yii::t('backend', 'Value with VAT'),
			],
			'valueWithoutVAT:currency:' . Yii::t('backend', 'Value without VAT'),
			'VATPercent',
			'date_at:date',
			'created_at:date',
			'updated_at:date',

			['class' => 'yii\grid\ActionColumn'],
		],
	]) ?>

</div>
