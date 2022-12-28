<?php

use backend\widgets\CsvForm;
use common\helpers\Html;
use common\helpers\StringHelper;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadSmsForm;
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
/* @var $onlyUser bool */
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

$multipleForm = empty($searchModel->dialer_id)
	&& ($assignUsers
		|| Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)
		|| Yii::$app->user->can(User::PERMISSION_LEAD_STATUS)
		|| Yii::$app->user->can(User::PERMISSION_LEAD_DIALER_MANAGER)
		|| Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)
	);

if ($multipleForm) {
	$dataProvider->getModels();
}
?>
<div class="lead-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p style="display: inline">
		<?= Html::a(Yii::t('lead', 'Phone Lead'), ['phone'], ['class' => 'btn btn-info']) ?>

		<?= CreateLeadBtnWidget::widget([
			'owner_id' => is_int($searchModel->user_id) ? $searchModel->user_id : null,
		]) ?>

		<?= Html::a(Yii::t('lead', 'Lead Reports'), ['report/index'], ['class' => 'btn btn-warning']) ?>

		<span class="btn-group">
			<?= Html::a(Yii::t('lead', 'Lead Reminders'), ['reminder/index'], ['class' => 'btn btn-danger']) ?>
			<?= Html::a(Html::icon('calendar'), ['/calendar/lead-reminder/index'], ['class' => 'btn btn-danger']) ?>
		</span>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_DIALER_MANAGER)
			? Html::a(Yii::t('lead', 'Dialers'), ['dialer/index'], ['class' => 'btn btn-primary'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_DUPLICATE)
			? Html::a(Yii::t('lead', 'Duplicates'), ['duplicate/index'], ['class' => 'btn btn-warning'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_MARKET)
			? Html::a(Yii::t('lead', 'Lead Markets'), ['market/index'], ['class' => 'btn btn-success'])
			: ''
		?>
		<?= Yii::$app->user->can(User::PERMISSION_LEAD_DELETE)
		&& $dataProvider->pagination->pageCount > 1
			?
			Html::a(
				Yii::t('lead', 'Delete ({count})', ['count' => $dataProvider->getTotalCount(),]),
				false,
				[
					'class' => 'btn btn-danger pull-right',
					'data' => [
						'method' => 'delete',
						'confirm' => Yii::t('lead', 'Are you sure you want to delete this items?'),
					],
				])

			: ''
		?>

	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>
	<div class="grid-before">


		<?= Yii::$app->user->can(User::PERMISSION_EXPORT) ? CsvForm::widget(['formOptions' => ['class' => 'pull-right',],]) : '' ?>

		<?php if ($multipleForm): ?>

			<?php
			SelectionForm::begin([
				'formWrapperSelector' => '.selection-form-wrapper',
				'gridId' => 'leads-grid',
			]);
			?>

			<div class="selection-form-wrapper hidden">

				<?= Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)
				&& $dataProvider->pagination->pageCount > 1
					? Html::a(
						Yii::t('lead', 'Send SMS: {count}', ['count' => count($searchModel->getAllIds($dataProvider->query)),]), ['sms/push-multiple',],
						count($searchModel->getAllIds($dataProvider->query)) < 6000
							? [
							'data' => [
								'method' => 'POST',
								'params' => ['leadsIds' => $searchModel->getAllIds($dataProvider->query),],
							],
							'class' => 'btn btn-success',
						]
							: [
							'disabled' => 'disabled',
							'title' => Yii::t('lead', 'For Send SMS records must be less then 6000.'),
							'aria-label' => 'For send',
							'class' => 'btn btn-success disabled',
						]
					)
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

				<?= $assignUsers
					? Html::submitButton(
						Yii::t('lead', 'Link Users'),
						[
							'class' => 'btn btn-info',
							'name' => 'route',
							'value' => 'user/assign',
						])
					: ''
				?>


				<?= $assignUsers
				&& $dataProvider->pagination->pageCount > 1
					? Html::a(
						Yii::t('lead', 'Link Users ({count})', ['count' => count($searchModel->getAllIds($dataProvider->query))]),
						['user/assign'],
						[
							'class' => 'btn btn-info',
							'data' => [
								'method' => 'POST',
								'params' => ['leadsIds' => $searchModel->getAllIds($dataProvider->query),],
							],
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
						Yii::t('lead', 'Change Status ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),
						['status/change'],
						[
							'class' => 'btn btn-warning',
							'data' => [
								'method' => 'POST',
								'params' => ['leadsIds' => $searchModel->getAllIds($dataProvider->query),],
							],
						])
					: ''
				?>

				<?= Yii::$app->user->can(User::PERMISSION_LEAD_DIALER_MANAGER)
					? Html::submitButton(
						Yii::t('lead', 'Assign to Dialer'),
						[
							'class' => 'btn btn-primary',
							'name' => 'route',
							'value' => 'dialer/create-multiple',
						])
					: ''
				?>

				<?= Yii::$app->user->can(User::PERMISSION_LEAD_DIALER_MANAGER)
				&& $dataProvider->pagination->pageCount > 1

					? Html::a(
						Yii::t('lead', 'Assign to Dialer ({ids})', ['ids' => count($searchModel->getAllIds($dataProvider->query))]),
						['dialer/create-multiple'],
						[
							'class' => 'btn btn-primary',
							'data' => [
								'method' => 'POST',
								'params' => ['leadsIds' => $searchModel->getAllIds($dataProvider->query),],
							],
						])
					: ''
				?>


				<?= Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)
					? Html::submitButton(
						Yii::t('lead', 'Move to Market'),
						[
							'class' => 'btn btn-success',
							'name' => 'route',
							'value' => 'market/create-multiple',
						])
					: ''
				?>

				<?= Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)
				&& $dataProvider->pagination->pageCount > 1

					? Html::a(
						Yii::t('lead', 'Move to Market ({count})', ['count' => count($searchModel->getAllIds($dataProvider->query))]),
						['market/create-multiple'],
						[
							'class' => 'btn btn-success',
							'data' => [
								'method' => 'POST',
								'params' => ['leadsIds' => $searchModel->getAllIds($dataProvider->query),],
							],
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
				'visible' => $multipleForm,
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
			],
			[
				'attribute' => 'phone',
				'format' => 'tel',
				'noWrap' => true,
				'width' => '124px',
			],
			[
				'attribute' => 'type_id',
				'value' => 'source.type',
				'contentBold' => true,
				'filter' => $searchModel::getTypesNames(),
				'label' => Yii::t('lead', 'Type'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => ['placeholder' => Yii::t('lead', 'Type'),],
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
				'value' => 'statusName',
				'filter' => $searchModel::getStatusNames(),
				'label' => Yii::t('lead', 'Status'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => ['placeholder' => Yii::t('lead', 'Status'),],
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
				'value' => function (ActiveLead $lead): string {
					if (!$lead->getSource()->getURL()) {
						return $lead->getSource()->getName();
					}
					return Html::a(Html::encode($lead->getSource()->getName()),
						$lead->getSource()->getURL(), ['target' => '_blank',]);
				},
				'format' => 'raw',
				'filter' => $searchModel->getSourcesNames(),
				'label' => Yii::t('lead', 'Source'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => ['placeholder' => Yii::t('lead', 'Source'),],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			'date_at:date',
		],
			$questionColumns,
			[
				[
					'class' => AddressColumn::class,
					'attribute' => 'customerAddress',
				],
				[
					'attribute' => 'reportsDetails',
					'format' => 'html',
					'value' => static function (ActiveLead $lead): string {
						$content = [];
						$smsCount = 0;
						foreach ($lead->reports as $report) {
							if ($report->status->show_report_in_lead_index) {
								$details = $report->getDetails();
								if (StringHelper::startsWith($details, LeadSmsForm::detailsPrefix())) {
									$smsCount++;
								} else {
									$content[] = Html::encode($details);
								}
							}
						}
						if ($smsCount) {
							$content[] = LeadSmsForm::detailsPrefix() . "<strong>$smsCount</strong>";
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
					'template' => '{view} {update} {report} {sms} {user} {reminder} {market} {delete}',
					'visibleButtons' => $visibleButtons,
					'buttons' => [
						'market' => static function (string $url, ActiveLead $model): string {
							if (Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)) {
								return Html::a('<i class="fa fa-bullhorn" aria-hidden="true"></i>',
									['market/create', 'id' => $model->getId()],
									[
										'title' => Yii::t('lead', 'Move to Market'),
										'aria-label' => Yii::t('lead', 'Move to Market'),
									]
								);
							}
							return '';
						},
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
