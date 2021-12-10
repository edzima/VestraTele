<?php

use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadReportSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\SerialColumn;
use common\widgets\GridView;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel LeadReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Reports');
?>
<div class="lead-report-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => SerialColumn::class],
			[
				'attribute' => 'lead_name',
				'value' => 'lead.name',
				'contentBold' => true,
				'label' => Yii::t('lead', 'Lead Name'),
			],
			[
				'attribute' => 'lead_status_id',
				'value' => 'lead.statusName',
				'filter' => LeadStatus::getNames(),
				'label' => Yii::t('lead', 'Current Status'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Current Status'),
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
				'attribute' => 'lead_phone',
				'value' => 'lead.phone',
				'format' => 'tel',
				'label' => Yii::t('lead', 'Phone'),
			],
			[
				'attribute' => 'lead_type_id',
				'value' => 'lead.source.type',
				'filter' => LeadType::getNamesWithDescription(),
				'label' => Yii::t('lead', 'Type'),
				'contentBold' => true,
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
				'attribute' => 'owner_id',
				'value' => 'owner',
				'filter' => LeadReportSearch::getOwnersNames(),
				'label' => Yii::t('lead', 'Owner'),
				'visible' => $searchModel->scenario !== LeadReportSearch::SCENARIO_OWNER,
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Owner'),
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
				'attribute' => 'status_id',
				'value' => 'status',
				'filter' => LeadStatus::getNames(),
				'label' => Yii::t('lead', 'Status'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Status'),
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
				'attribute' => 'old_status_id',
				'value' => 'oldStatus',
				'filter' => LeadStatus::getNames(),
				'label' => Yii::t('lead', 'Old Status'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('lead', 'Old Status'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			'answersQuestions',
			'details:text',
			'created_at:datetime',
			'updated_at:datetime',

			[
				'class' => ActionColumn::class,
				'template' => '{report} {sms} {view} {update} {delete}',
				'buttons' => [
					'report' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('comment'),
							['report', 'id' => $report->lead_id],
							[
								'title' => Yii::t('lead', 'Create Report'),
								'aria-title' => Yii::t('lead', 'Create Report'),
							]
						);
					},
					'view' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('eye-open'),
							['lead/view', 'id' => $report->lead_id], [
								'title' => Yii::t('yii', 'View'),
								'aria-title' => Yii::t('yii', 'View'),
							]
						);
					},
					'sms' => static function (string $url, LeadReport $model): string {
						if (Yii::$app->user->can(User::PERMISSION_SMS)) {
							return Html::a('<i class="fa fa-envelope" aria-hidden="true"></i>',
								['sms/push', 'id' => $model->lead_id],
								[
									'title' => Yii::t('lead', 'Send SMS'),
									'aria-label' => Yii::t('lead', 'Send SMS'),
								]
							);
						}
						return '';
					},
				],
			],
		],
	]); ?>


</div>
