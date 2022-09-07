<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadDialerSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this View */
/* @var $model LeadDialerSearch */

$this->title = Yii::t('lead', 'Dialers - {dialer}', [
	'dialer' => $model->getDialerName(),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="dialer-lead-index">

	<?= $this->render('_nav', [
		'model' => $model,
	]) ?>

	<?= $this->render('_search', [
		'model' => $model,
	]) ?>


	<?= GridView::widget([
		'caption' => Yii::t('lead', 'Calling'),
		'dataProvider' => $model->getCallingDataProvider(),
		'columns' => [
			'lead.phone:tel',
			[
				'attribute' => 'lead.name',
				'contentBold' => true,
				'noWrap' => true,
			],
			'created_at:datetime',
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
				'buttons' => [
					'view' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('eye-open'),
							['lead/view', 'id' => $report->lead_id], [
								'title' => Yii::t('yii', 'View'),
								'aria-title' => Yii::t('yii', 'View'),
							]
						);
					},
				],
			],
		],
	]) ?>

	<?= GridView::widget([
		'caption' => Yii::t('lead', 'Received'),
		'filterModel' => $model,
		'dataProvider' => $model->getAnsweredDataProvider(),
		'columns' => [
			'lead.phone:tel',
			[
				'attribute' => 'lead.name',
				'contentBold' => true,
				'noWrap' => true,
			],
			'created_at:datetime',
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
				'buttons' => [
					'view' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('eye-open'),
							['lead/view', 'id' => $report->lead_id], [
								'title' => Yii::t('yii', 'View'),
								'aria-title' => Yii::t('yii', 'View'),
							]
						);
					},
				],
			],
		],
	]) ?>

	<?= GridView::widget([
		'caption' => Yii::t('lead', 'Missed'),
		'dataProvider' => $model->getNotAnsweredDataProvider(),
		'columns' => [
			'lead.phone:tel',
			[
				'attribute' => 'lead.name',
				'contentBold' => true,
				'noWrap' => true,
			],
			'created_at:datetime',
			[
				'class' => ActionColumn::class,
				'template' => '{view}',
				'buttons' => [
					'view' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('eye-open'),
							['lead/view', 'id' => $report->lead_id], [
								'title' => Yii::t('yii', 'View'),
								'aria-title' => Yii::t('yii', 'View'),
							]
						);
					},
				],
			],
		],
	]) ?>

	<?= GridView::widget([
		'dataProvider' => $model->getToCallDataProvider(),
		'caption' => Yii::t('lead', 'Calls queue'),
		'columns' => [
			'phone:tel',
			[
				'attribute' => 'name',
				'contentBold' => true,
				'noWrap' => true,
			],
			[
				'attribute' => 'type_id',
				'value' => 'source.type',
				'contentBold' => true,
				'filter' => LeadType::getNamesWithDescription(),
				'label' => Yii::t('lead', 'Type'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Type'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			[
				'attribute' => 'source_id',
				'value' => static function (ActiveLead $lead): string {
					if (!$lead->getSource()->getURL()) {
						return $lead->getSource()->getName();
					}
					return Html::a(Html::encode($lead->getSource()->getName()),
						['source/view', 'id' => $lead->getSourceId()], [
							'target' => '_blank',
						]);
				},
				'format' => 'raw',
				'filter' => ArrayHelper::map(LeadSource::getModels(), 'id', 'name'),
				'label' => Yii::t('lead', 'Source'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Source'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			[
				'label' => Yii::t('lead', 'Reports Count'),
				'value' => static function (ActiveLead $lead): int {
					return count($lead->reports);
				},
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view} {delete}',
				'buttons' => [
					'view' => static function (string $url, ActiveLead $lead): string {
						return Html::a(
							Html::icon('eye-open'),
							['lead/view', 'id' => $lead->getId()], [
								'title' => Yii::t('yii', 'View'),
								'aria-title' => Yii::t('yii', 'View'),
							]
						);
					},
				],
			],
		],
	])
	?>


</div>




