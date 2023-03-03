<?php

use common\helpers\Html;
use common\helpers\StringHelper;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadSmsForm;
use common\modules\lead\models\searches\LeadSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AddressColumn;
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

$multipleForm = empty($searchModel->dialer_id)
	&& ($assignUsers
		|| Yii::$app->user->can(User::PERMISSION_MULTIPLE_SMS)
		|| Yii::$app->user->can(User::PERMISSION_LEAD_STATUS)
		|| Yii::$app->user->can(User::PERMISSION_LEAD_DIALER_MANAGER)
		|| Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)
	);

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

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'id' => 'leads-grid',
	'pjax' => true,
	'pjaxSettings' => [
		'beforeGrid' => $this->render('_grid_actions', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'assignUsers' => $assignUsers,
		]),
		'afterGrid' => Html::endForm(),
	],

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
			'value' => 'statusName',
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
			'value' => function (ActiveLead $lead): string {
				if (!$lead->getSource()->getURL()) {
					return $lead->getSource()->getName();
				}
				return Html::a(Html::encode($lead->getSource()->getName()),
					$lead->getSource()->getURL(), [
						'target' => '_blank',
					]);
			},
			'format' => 'raw',
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
				'options' => ['multiple' => true],
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
])
?>
