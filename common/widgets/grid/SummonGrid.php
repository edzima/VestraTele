<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\models\issue\search\SummonSearch;
use common\models\issue\Summon;
use common\models\user\User;
use common\widgets\GridView;
use DateTime;
use kartik\select2\Select2;
use Yii;
use yii\base\InvalidConfigException;

/**
 * @property-read SummonSearch $filterModel
 */
class SummonGrid extends GridView {

	public const VALUE_TYPE_NAME_SHORT = 'type.short_name';
	public const VALUE_TYPE_NAME = 'type.name';

	public $id = 'summon-grid';

	/** @todo add note link */
	public $actionColumn = [
		'class' => ActionColumn::class,
		'template' => '{realize} {note} {view} {update} {delete}',
	];

	public ?string $noteRoute = '/issue/note/create-summon';

	public string $issueColumn = IssueColumn::class;

	public string $issueStageColumn = IssueStageColumn::class;
	public string $valueType = self::VALUE_TYPE_NAME;

	public bool $withTitle = true;
	public bool $withDocs = true;
	public bool $withTitleWithDocs = false;
	public bool $withIssue = true;
	public bool $withCustomer = true;
	public bool $withCaption = false;
	public bool $withCustomerPhone = true;
	public bool $withContractor = true;

	public bool $withType = true;
	public bool $withOwner = true;
	public bool $withUpdatedAt = true;
	public bool $withDeadline = true;
	public bool $withStatus = true;

	public bool $withIssueStage = false;
	public bool $withRealizedAt = false;

	public bool $rowColors = true;
	public string $realizedClass = 'success';
	public string $unrealizedClass = 'half-transparent';
	public string $deadlineExceededClass = 'danger';
	public string $tightDeadlineClass = 'warning';

	public int $tightDeadlineDays = 0;
	public bool $withDocsCountSummary = false;

	public function init(): void {
		if ($this->filterModel !== null && !$this->filterModel instanceof SummonSearch) {
			throw new InvalidConfigException('$filterModel must be instance of: ' . SummonSearch::class . '.');
		}
		if (!empty($this->noteRoute) && Yii::$app->user->can(User::PERMISSION_NOTE)) {
			$this->actionColumn['buttons']['note'] = function (string $url, Summon $model): string {
				return Html::a('<i class="fa fa-comments" aria-hidden="true"></i>',
					[$this->noteRoute, 'id' => $model->id],
					[
						'title' => Yii::t('issue', 'Create Note'),
						'aria-label' => Yii::t('issue', 'Create Note'),
					]
				);
			};
		}

		if (!isset($this->actionColumn['buttons']['realize'])) {
			$this->actionColumn['buttons']['realize'] = static function (string $url, Summon $model): string {
				return Html::a(Html::icon('check'),
					$url,
					[
						'title' => Yii::t('issue', 'Realize it'),
						'aria-label' => Yii::t('issue', 'Realize it'),
						'data-method' => 'POST',
					],
				);
			};
		}

		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		if (!empty($this->id) && !isset($this->options['id'])) {
			$this->options['id'] = $this->id;
		}

		if ($this->withCaption && empty($this->caption)) {
			$this->caption = Yii::t('common', 'Summons');
		}
		if ($this->rowColors && empty($this->rowOptions)) {
			$this->rowOptions = function (Summon $model): array {
				return $this->colorRowOptions($model);
			};
		}

		parent::init();
	}

	public function colorRowOptions(Summon $model): array {
		if ($model->isRealized()) {
			return [
				'class' => $this->realizedClass,
			];
		}
		if ($model->isUnrealized()) {
			return [
				'class' => $this->unrealizedClass,
			];
		}
		if (!empty($model->deadline_at)) {
			$deadline = new DateTime($model->deadline_at);
			$nowDiff = $deadline->diff(new DateTime());
			if (!$nowDiff->invert) {
				$days = $nowDiff->days;
				if ($days >= 0 && $days > $this->tightDeadlineDays) {
					return [
						'class' => $this->deadlineExceededClass,
					];
				}
				if ($nowDiff->days <= $this->tightDeadlineDays) {
					return [
						'class' => $this->tightDeadlineClass,
					];
				}
			}
		}
		return [];
	}

	public function defaultColumns(): array {
		return [
			[
				'class' => $this->issueColumn,
				'visible' => $this->withIssue,
			],
			[
				'class' => $this->issueStageColumn,
				'visible' => $this->withIssueStage,
				'attribute' => 'issueStageId',
				'contentCenter' => true,
			],
			[
				'class' => CustomerDataColumn::class,
				'visible' => $this->withCustomer,
			],

			[
				'attribute' => 'customerPhone',
				'value' => 'issue.customer.profile.phone',
				'label' => Yii::t('common', 'Phone number'),
				'format' => 'tel',
				'noWrap' => true,
				'visible' => $this->withCustomerPhone,
			],
			[
				'attribute' => 'type_id',
				'value' => $this->valueType,
				'filter' => SummonSearch::getTypesNames(),
				'contentBold' => true,
				'noWrap' => true,
				'visible' => $this->withType,
			],

			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => SummonSearch::getStatusesNames(),
				'options' => [
					'style' => [
						'min-width' => '115px',
					],
				],
				'visible' => $this->withStatus,
			],
			[
				'attribute' => 'title',
				'contentOptions' => ['style' => 'width: 35%;'],
				'visible' => $this->withTitle,
			],
			[
				'attribute' => 'titleWithDocs',
				'contentOptions' => ['style' => 'width: 35%;'],
				'visible' => $this->withTitleWithDocs,
			],
			[
				'attribute' => 'doc_types_ids',
				'value' => function (Summon $summon): ?string {
					$docsLink = $summon->docsLink;
					if (empty($docsLink)) {
						return null;
					}
					$confirmed = [];
					$notConfirmed = [];
					foreach ($docsLink as $docLink) {
						if ($docLink->isConfirmed()) {
							$confirmed[] = Html::encode($docLink->doc->name);
						} else {
							$notConfirmed[] = Html::encode($docLink->doc->name);
						}
					}
					$content = '';
					if (!empty($notConfirmed)) {
						$content .= Html::tag('strong', Yii::t('issue', 'To Do: {count}', [
								'count' => count($notConfirmed),
							]))
							. Html::ul($notConfirmed, [
								'class' => ['mb-0'],
							]);
					}
					if (!empty($confirmed)) {
						$content .= Html::tag('strong', Yii::t('issue', 'Confirmed: {count}', [
								'count' => count($confirmed),
							]))
							. Html::ul($confirmed, [
								'class' => ['mb-0 text-line_trough'],
							]);
					}
					return $content;
				},
				'format' => 'html',
				'filter' => SummonSearch::getDocTypesNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'multiple' => true,
						'placeholder' => SummonSearch::instance()->getAttributeLabel('doc_types_ids'),
					],
				],
				'options' => [
					'style' => [
						'min-width' => '250px',
					],
				],
				'visible' => $this->withDocs,
			],
			[
				'attribute' => 'docsCountSummary',
				'noWrap' => true,
				'value' => function (Summon $model): string {
					$summary = $model->getDocsCountSummary();
					if ($summary) {
						return $summary;
					}
					return '';
				},
				'visible' => $this->withDocsCountSummary,
			],
			[
				'attribute' => 'start_at',
				'format' => 'date',
				'noWrap' => true,
			],
			[
				'attribute' => 'deadline_at',
				'format' => 'date',
				'noWrap' => true,
				'visible' => $this->withDeadline,
			],
			[
				'attribute' => 'updated_at',
				'format' => 'date',
				'visible' => $this->withUpdatedAt,
				'noWrap' => true,
			],
			[
				'attribute' => 'realized_at',
				'format' => 'date',
				'visible' => $this->withRealizedAt,
				'noWrap' => true,
			],
			[
				'attribute' => 'owner_id',
				'value' => 'owner',
				'filter' => $this->filterModel ? $this->filterModel->getOwnersNames() : [],
				'filterType' => static::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('common', 'Owner'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
				'visible' => $this->withOwner,
			],
			[
				'attribute' => 'contractor_id',
				'value' => 'contractor',
				'filter' => $this->filterModel ? $this->filterModel->getContractorsNames() : [],
				'filterType' => static::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('common', 'Contractor'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
				'visible' => $this->withContractor,
			],
			$this->actionColumn,
		];
	}

}
