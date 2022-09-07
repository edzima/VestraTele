<?php

use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\provision\Provision;
use common\models\provision\ProvisionSearch;
use common\models\provision\ToUserGroupProvisionSearch;
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

	<p>
		<?= Html::a(Yii::t('provision', 'Reports'), [
			'report/index',
			Html::getInputName(ToUserGroupProvisionSearch::instance(), 'dateFrom') => $searchModel->dateFrom,
			Html::getInputName(ToUserGroupProvisionSearch::instance(), 'dateTo') => $searchModel->dateTo,
			Html::getInputName(ToUserGroupProvisionSearch::instance(), 'to_user_id') => $searchModel->to_user_id,
		], ['class' => 'btn btn-success']) ?>

		<?= Html::a(Yii::t('settlement', 'Without provisions'), [
			'/settlement/calculation/without-provisions',
		], [
			'class' => 'btn btn-warning',
		]) ?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'class' => IssueColumn::class,
			],
			[
				'attribute' => 'settlementTypes',
				'filter' => $searchModel::getSettlementTypesNames(),
				'label' => $searchModel->getAttributeLabel('settlementTypes'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => $searchModel->getAttributeLabel('settlementTypes'),
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
			'pay.value:currency',

			'provision',
			'value:currency',
			$searchModel->isUnpaid() ? 'pay.deadline_at:date' : 'pay.pay_at:date',
			[
				'class' => ActionColumn::class,
				'template' => '{update} {delete}',
			],
		],
	]); ?>


</div>
