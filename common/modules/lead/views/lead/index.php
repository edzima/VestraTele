<?php

use backend\widgets\CsvForm;
use common\helpers\Html;
use common\helpers\Url;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\lead\widgets\CreateLeadBtnWidget;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AddressColumn;
use common\widgets\grid\SerialColumn;
use common\widgets\GridView;
use kartik\grid\CheckboxColumn;
use yii\bootstrap\ButtonDropdown;

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

?>
<div class="lead-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Phone Lead'), ['phone'], ['class' => 'btn btn-info']) ?>

		<?= CreateLeadBtnWidget::widget([
			'owner_id' => is_int($searchModel->user_id) ? $searchModel->user_id : null,
		]) ?>

		<?= Html::a(Yii::t('lead', 'Lead Reports'), ['report/index'], ['class' => 'btn btn-warning']) ?>

	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= Yii::$app->user->can(User::PERMISSION_EXPORT) ? CsvForm::widget() : '' ?>

	<?php if ($assignUsers || Yii::$app->user->can(User::PERMISSION_SMS)): ?>

		<?= Html::beginForm('', 'POST', [
			'id' => 'form-lead-multiple-actions',
			'data-pjax' => '',
		]) ?>

		<?= Yii::$app->user->can(User::PERMISSION_SMS)
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
				'class' => 'btn btn-success',
				'name' => 'route',
				'value' => 'user/assign',
			])
			: ''
		?>

	<?php endif; ?>

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
			'name',
			'phone:tel',
			[
				'attribute' => 'type_id',
				'value' => 'source.type',
				'filter' => $searchModel::getTypesNames(),
				'label' => Yii::t('lead', 'Type'),
			],
			[
				'attribute' => 'status_id',
				'value' => 'status',
				'filter' => $searchModel::getStatusNames(),
				'label' => Yii::t('lead', 'Status'),
			],
			[
				'attribute' => 'source_id',
				'value' => 'source',
				'filter' => $searchModel->getSourcesNames(),
				'label' => Yii::t('lead', 'Source'),
			],
			[
				'attribute' => 'campaign_id',
				'value' => 'campaign',
				'filter' => $searchModel->getCampaignNames(),
				'label' => Yii::t('lead', 'Campaign'),
				'visible' => $searchModel->scenario !== LeadSearch::SCENARIO_USER,
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
							$content[] = $report->getDetails();
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

	<?= $assignUsers ? Html::endForm() : '' ?>

</div>
