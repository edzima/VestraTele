<?php

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\helpers\StringHelper;
use common\helpers\Url;
use common\models\user\User;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadSmsForm;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\lead\Module;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AddressColumn;
use common\widgets\grid\DateTimeColumn;
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
	'filterAllowedEmpty' => false,
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
	'rowOptions' => function (ActiveLead $model) use ($searchModel, $dataProvider): array {
		$options = [];
		$fromMarket = false;
		$inMarket = false;
		if ($searchModel->leadMarketId($model->getId(), $dataProvider->getKeys())) {
			$inMarket = true;
			$fromMarket = Module::getInstance()->market->isFromMarket(
				$model->getUsers(),
				Yii::$app->user->getId()
			);
			if ($fromMarket) {
				Html::addCssClass($options, 'lead-from-market');
			}
			Html::addCssClass($options, 'lead-on-market');
		}
		if (!$inMarket || $fromMarket) {
			$hours = $model->getDeadlineHours();
			if ($hours !== null) {
				if ($hours > 0) {
					Html::addCssClass($options, 'danger');
				} else {
					$warning = $model->status->hours_deadline_warning;
					if ($warning && $hours * -1 < $warning) {
						Html::addCssClass($options, 'warning');
					}
				}
			}
		}

		return $options;
	},
	'columns' => array_merge([
		[
			'class' => CheckboxColumn::class,
			'visible' => $multipleForm,
			'rowHighlight' => false,
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
					'multiple' => true,
					'dropdownAutoWidth' => true,
				],
			],
		],
		[
			'attribute' => 'source_id',
			'value' => function (ActiveLead $lead) use ($searchModel, $dataProvider): string {
				$tag = '';
				if (Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)) {
					$marketId = $searchModel->leadMarketId($lead->getId(), $dataProvider->getKeys());
					if ($marketId) {
						$from = Module::getInstance()->market->isFromMarket(
							$lead->getUsers(),
							Yii::$app->user->getId()
						);
						$text = $from ? Yii::t('lead', 'From Market') : Yii::t('lead', 'On Market');

						$tag = Html::a($text . ' ' . Html::faicon('bullhorn'),
							['market/view', 'id' => $marketId], [
								'class' => 'label white-label lead-from-market-label',
								'data-pjax' => 0,
							]
						);
						$tag = Html::tag('div', $tag, ['class' => 'tags-wrapper text-center']);
					}
				}
				if (!$lead->getSource()->getURL()) {
					return $lead->getSource()->getName() . $tag;
				}
				return Html::a(Html::encode($lead->getSource()->getName()),
						$lead->getSource()->getURL(), [
							'target' => '_blank',
						]) . $tag;
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
		[
			'class' => DateTimeColumn::class,
			'attribute' => 'date_at',
		],
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
				'class' => DateTimeColumn::class,
				'attribute' => 'newestReportAt',
				'noWrap' => true,
				'value' => function (ActiveLead $lead): ?string {
					$reports = $lead->reports;
					if (empty($reports)) {
						return null;
					}
					return max(ArrayHelper::getColumn($reports, 'created_at'));
				},
				'label' => Yii::t('lead', 'Newest Report At'),
			],
			[
				'attribute' => 'deadlineType',
				'filter' => LeadSearch::getDeadlineNames(),
				'format' => 'raw',
				'value' => function (ActiveLead $lead): ?string {
					$deadline = $lead->getDeadline();
					if ($deadline) {
						return Html::a(
							Yii::$app->formatter->asDate($deadline),
							['deadline', 'id' => $lead->getId(), 'returnUrl' => Url::current()], [
							'aria-label' => Yii::t('lead', 'Update Deadline'),
							'title' => Yii::t('lead', 'Update Deadline'),
							'data-pjax' => 0,
						]);
					}
					return null;
				},
				'label' => Yii::t('lead', 'Deadline'),
				'noWrap' => true,
				'contentBold' => true,
			],
			[
				'attribute' => 'reportStatusCount',
				'value' => function (ActiveLead $lead) use ($searchModel, $dataProvider): int {
					return $searchModel->getReportStatusCount($lead->getId(), $dataProvider->getKeys());
				},
				'visible' => !empty($searchModel->reportStatus),
				'label' => Yii::t('lead', 'Report Status Count'),
			],
			[
				'class' => ActionColumn::class,
				'template' => '{view} {update} {report} {sms} {user} {reminder} {market} {delete}',
				'visibleButtons' => $visibleButtons,
				'buttons' => [
					'market' => function (string $url, ActiveLead $model) use ($searchModel, $dataProvider): string {
						if (Yii::$app->user->can(User::PERMISSION_LEAD_MARKET)
							&& empty($searchModel->leadMarketId($model->getId(), $dataProvider->getKeys()))) {
							return Html::a('<i class="fa fa-bullhorn" aria-hidden="true"></i>',
								['market/create', 'id' => $model->getId()],
								[
									'title' => Yii::t('lead', 'Move to Market'),
									'aria-label' => Yii::t('lead', 'Move to Market'),
									'data-pjax' => 0,
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
								'data-pjax' => 0,
							]
						);
					},
					'report' => static function (string $url, ActiveLead $lead): string {
						return Html::a(
							Html::icon('comment'),
							['report/report', 'id' => $lead->getId(), 'hash' => $lead->getHash()],
							[
								'title' => Yii::t('lead', 'Create Report'),
								'aria-title' => Yii::t('lead', 'Create Report'),
								'data-pjax' => 0,
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
								'data-pjax' => 0,
							]);
					},
					'sms' => static function (string $url, ActiveLead $model): string {
						if (Yii::$app->user->can(User::PERMISSION_SMS)) {
							return Html::a('<i class="fa fa-envelope" aria-hidden="true"></i>',
								['sms/push', 'id' => $model->getId()],
								[
									'title' => Yii::t('lead', 'Send SMS'),
									'aria-label' => Yii::t('lead', 'Send SMS'),
									'data-pjax' => 0,
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
