<?php

use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadReportSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\SelectionForm;
use common\widgets\grid\SerialColumn;
use common\widgets\GridView;
use kartik\grid\CheckboxColumn;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel LeadReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('lead', 'Lead Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Reports');

$multipleForm = Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)
	|| Yii::$app->user->can(User::PERMISSION_LEAD_STATUS);

if ($multipleForm) {
	$dataProvider->getModels();
}

?>
<div class="lead-report-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Html::faicon('bar-chart'), ['chart', 'fromAt' => $searchModel->from_at, 'toAt' => $searchModel->to_at], ['class' => 'btn btn-success', 'title' => Yii::t('lead', 'Charts')]) ?>
	</p>


	<?= $this->render('_search', [
		'model' => $searchModel,
		'action' => ['index'],
	]) ?>

	<?php if ($multipleForm): ?>
		<div class="grid-before">
			<?php
			$ids = $searchModel->getAllLeadsIds($dataProvider->query);
			SelectionForm::begin([
				'formWrapperSelector' => '.selection-form-wrapper',
				'gridId' => 'leads-report-grid',
			]);
			?>

			<div class="selection-form-wrapper hidden">

				<?= Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)
				&& $dataProvider->pagination->pageCount > 1
				&& count($ids) < 6000
					? Html::a(
						Yii::t('lead', 'Send SMS: {count}', [
							'count' => count($ids),
						]), [
						'sms/push-multiple',
					],
						[
							'data' => [
								'method' => 'POST',
								'params' => [
									'leadsIds' => $ids,
								],
							],
							'class' => 'btn btn-success',
						])
					: ''
				?>

				<?= Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)
					? Html::submitButton(
						Yii::t('lead', 'Send SMS'),
						[
							'class' => 'btn btn-success',
							'name' => 'route',
							'value' => 'sms/push-multiple',
						])
					: ''
				?>

				<?= Yii::$app->user->can(User::PERMISSION_LEAD_STATUS)
					? Html::submitButton(
						Yii::t('lead', 'Change Status'),
						[
							'class' => 'btn btn-warning',
							'name' => 'route',
							'value' => 'status/change',
						])
					: ''
				?>

				<?= Yii::$app->user->can(User::PERMISSION_LEAD_STATUS)
				&& $dataProvider->pagination->pageCount > 1

					? Html::a(
						Yii::t('lead', 'Change Status ({ids})', ['ids' => count($ids)]),
						['status/change'],
						[
							'class' => 'btn btn-warning',
							'data' => [
								'method' => 'POST',
								'params' => [
									'leadsIds' => $ids,
								],
							],
							'value' => 'status/change',
						])
					: ''
				?>

			</div>
		</div>

	<?php endif; ?>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'id' => 'leads-report-grid',
		'columns' => [
			[
				'class' => CheckboxColumn::class,
				'visible' => $multipleForm,
				'checkboxOptions' => function (LeadReport $model, $key, $index, $column) {
					return ['value' => $model->lead_id];
				},
			],
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
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('lead_status_id'),
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
				'filter' => $searchModel->getOwnersNames(),
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
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('status_id'),
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
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('old_status_id'),
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
				'attribute' => 'deleted_at',
				'format' => 'datetime',
				'noWrap' => true,
				'visible' => $searchModel->scenario !== LeadReportSearch::SCENARIO_OWNER && ($searchModel->withoutDeleted === null || $searchModel->withoutDeleted),
			],
			[
				'class' => ActionColumn::class,
				'template' => '{report} {sms} {view} {update} {delete}',
				'visibleButtons' => [
					'delete' => function (LeadReport $report) use ($searchModel): bool {
						return $searchModel->scenario !== LeadReportSearch::SCENARIO_OWNER
							|| $report->owner_id === Yii::$app->user->getId();
					},
					'update' => function (LeadReport $report) use ($searchModel): bool {
						return $searchModel->scenario !== LeadReportSearch::SCENARIO_OWNER
							|| $report->owner_id === Yii::$app->user->getId();
					},
				],
				'buttons' => [
					'report' => static function (string $url, LeadReport $report): string {
						return Html::a(
							Html::icon('comment'),
							['report', 'id' => $report->lead_id, 'hash' => $report->lead->getHash()],
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

	<?php if ($multipleForm) {
		SelectionForm::end();
	}
	?>

</div>
