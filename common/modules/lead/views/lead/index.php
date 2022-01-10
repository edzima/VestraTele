<?php

use backend\widgets\CsvForm;
use common\helpers\Html;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\lead\widgets\CreateLeadBtnWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AddressColumn;
use common\widgets\grid\SelectionForm;
use common\widgets\grid\SerialColumn;
use common\widgets\GridView;
use kartik\grid\CheckboxColumn;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $assignUsers bool */
/* @var $visibleButtons array */

$this->title = Yii::t('lead', 'Leads');
$this->params['breadcrumbs'][] = $this->title;

$questionColumns = [];
foreach (LeadSearch::questions() as $question) {
	//@todo add input placeholders from $question->placeholder
	$questionColumns[] = [
		'attribute' => LeadSearch::generateQuestionAttribute($question->id),
		'label' => $question->name,
		'format' => $question->hasPlaceholder() ? 'text' : 'boolean',
		'value' => static function (ActiveLead $lead) use ($question): ?string {
			if ($question->hasPlaceholder()) {
				return $lead->answers[$question->id]->answer ?? null;
			}
			return isset($lead->answers[$question->id]);
		},
	];
}

$multipleForm = $assignUsers || Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS);
?>
<div class="lead-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div>
		<?= Html::a(Yii::t('lead', 'Phone Lead'), ['phone'], ['class' => 'btn btn-info']) ?>

		<?= CreateLeadBtnWidget::widget([
			'owner_id' => is_int($searchModel->user_id) ? $searchModel->user_id : null,
		]) ?>

		<?= Html::a(Yii::t('lead', 'Lead Reports'), ['report/index'], ['class' => 'btn btn-warning']) ?>

	</div>

	<?= $this->render('_search', ['model' => $searchModel]) ?>
	<div class="grid-before">


		<?= Yii::$app->user->can(User::PERMISSION_EXPORT) ? CsvForm::widget([
			'formOptions' => [
				'class' => 'pull-right',
			],
		]) : '' ?>

		<?php if ($multipleForm): ?>

			<?php
			SelectionForm::begin([
				'formWrapperSelector' => '.selection-form-wrapper',
				'gridId' => 'leads-grid',
			]);
			?>


			<div class="selection-form-wrapper hidden">


				<?= Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)
				&& !empty(($allIds = $searchModel->getAllIds($dataProvider->query))
					&& count($allIds) < 6000
				)
					? Html::a(
						Yii::t('lead', 'Send SMS: {count}', [
							'count' => count($allIds),
						]), [
						'sms/push-multiple',
					],
						[
							'data' => [
								'method' => 'POST',
								'params' => [
									'leadsIds' => $allIds,
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

				<?= $assignUsers ? Html::submitButton(
					Yii::t('lead', 'Link users'),
					[
						'class' => 'btn btn-info',
						'name' => 'route',
						'value' => 'user/assign',
					])
					: ''
				?>

			</div>

		<?php endif; ?>

	</div>
	<div class="clearfix"></div>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'id' => 'leads-grid',
		'columns' => array_merge([
			[
				'class' => CheckboxColumn::class,
				'visible' => $assignUsers,
			],
			['class' => SerialColumn::class],
			[
				'attribute' => 'owner_id',
				'label' => Yii::t('lead', 'Owner'),
				'value' => 'owner',
				'visible' => $searchModel->scenario !== LeadSearch::SCENARIO_USER,
			],
			[
				'attribute' => 'name',
				'contentBold' => true,
				'noWrap' => true,
			],
			'phone:tel',
			[
				'attribute' => 'type_id',
				'value' => 'source.type',
				'contentBold' => true,
				'filter' => $searchModel::getTypesNames(),
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
				'attribute' => 'status_id',
				'value' => 'status',
				'filter' => $searchModel::getStatusNames(),
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
				'attribute' => 'source_id',
				'value' => 'source',
				'filter' => $searchModel->getSourcesNames(),
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
			'date_at',
		],
			$questionColumns,
			[
				[
					'class' => AddressColumn::class,
					'attribute' => 'customerAddress',
				],
				[
					'attribute' => 'reportsDetails',
					'value' => static function (ActiveLead $lead): string {
						$content = [];
						foreach ($lead->reports as $report) {
							if ($report->status->show_report_in_lead_index) {
								$content[] = $report->getDetails();
							}
						}
						$content = array_filter($content, static function ($value): bool {
							return !empty(trim($value));
						});
						return implode(', ', $content);
					},
					'label' => Yii::t('lead', 'Reports Details'),
				],
				[
					'attribute' => 'reportsAnswers',
					'value' => static function (ActiveLead $lead): string {
						$content = [];
						foreach ($lead->reports as $report) {
							$content[] = $report->getAnswersQuestions();
						}
						$content = array_filter($content, static function ($value): bool {
							return !empty(trim($value));
						});
						return implode(', ', $content);
					},
					'label' => Yii::t('lead', 'Reports Answers'),
				],
				[
					'class' => ActionColumn::class,
					'template' => '{view} {update} {report} {sms} {user} {reminder} {delete}',
					'visibleButtons' => $visibleButtons,
					'buttons' => [
						'user' => static function (string $url, ActiveLead $lead): string {
							return Html::a(
								Html::icon('plus'),
								['user/assign-single', 'id' => $lead->getId()],
								[
									'title' => Yii::t('lead', 'Assign User'),
									'aria-title' => Yii::t('lead', 'Assign User'),
								]
							);
						},
						'report' => static function (string $url, ActiveLead $lead): string {
							return Html::a(
								Html::icon('comment'),
								['report/report', 'id' => $lead->getId()],
								[
									'title' => Yii::t('lead', 'Create Report'),
									'aria-title' => Yii::t('lead', 'Create Report'),
								]
							);
						},
						'reminder' => static function (string $url, ActiveLead $lead): string {
							return Html::a(
								Html::icon('calendar'),
								['reminder/create', 'id' => $lead->getId()],
								[
									'title' => Yii::t('lead', 'Create Reminder'),
									'aria-title' => Yii::t('lead', 'Create Reminder'),
								]);
						},
						'sms' => static function (string $url, ActiveLead $model): string {
							if (Yii::$app->user->can(User::PERMISSION_SMS)) {
								return Html::a('<i class="fa fa-envelope" aria-hidden="true"></i>',
									['sms/push', 'id' => $model->getId()],
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
			]),
	]) ?>

	<?php if ($multipleForm) {
		SelectionForm::end();
	}
	?>

</div>
