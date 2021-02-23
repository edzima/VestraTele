<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\provision\Provision;
use common\models\provision\ProvisionSearch;
use common\widgets\grid\CustomerDataColumn;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ProvisionSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('provision', 'Provisions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-index">

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => IssueColumn::class,
				'issueAttribute' => 'pay.issue',
			],
			[
				'attribute' => 'calculationTypes',
				'filter' => $searchModel::getCalculationTypesNames(),
				'label' => $searchModel->getAttributeLabel('calculationTypes'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => $searchModel->getAttributeLabel('calculationTypes'),
					],
					'pluginOptions' => [
						'width' => '120px',
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
				],
				'value' => static function (Provision $model): string {
					return Html::a($model->pay->calculation->getTypeName(), ['/settlement/calculation/view', 'id' => $model->pay->calculation->id]);
				},
				'format' => 'raw',
			],
			[
				'attribute' => 'type_id',
				'filter' => $searchModel::getTypesNames(),
				'label' => $searchModel->getAttributeLabel('type'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => $searchModel->getAttributeLabel('type'),
					],
					'size' => Select2::SIZE_SMALL,
					'showToggleAll' => false,
					'pluginOptions' => [
						'width' => '140px',
					],
				],
				'value' => 'type.name',
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'pay.issue.customer.fullName',
			],
			[
				'label' => 'Płatność',
				'value' => 'pay.partInfo',
			],
			'toUser',
			'fromUserString',
			'provisionPercent',
			'value:currency',
			'pay.value:currency',
			$searchModel->isNotPayed() ? 'pay.deadline_at:date' : 'pay.pay_at:date',
			[
				'class' => ActionColumn::class,
				'template' => '{update} {delete}',
			],
		],
	]); ?>


</div>
