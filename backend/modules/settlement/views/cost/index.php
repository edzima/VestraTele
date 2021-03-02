<?php

use backend\modules\settlement\models\search\IssueCostSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use kartik\grid\SerialColumn;

/* @var $this yii\web\View */
/* @var $model IssueCostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Costs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-cost-index">

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
				'attribute' => 'user_id',
				'value' => 'user',
				'label' => Yii::t('backend', 'User'),
				'filter' => IssueCostSearch::getUsersNames(),
			],
			[
				'attribute' => 'withSettlements',
				'value' => 'hasSettlements',
				'label' => Yii::t('settlement', 'With settlements'),
				'noWrap' => true,
				'format' => 'boolean',
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
