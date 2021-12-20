<?php

namespace common\widgets\grid;

use common\models\issue\search\SummonSearch;
use common\widgets\GridView;
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
		'template' => '{note} {view} {update} {delete}',
	];

	public string $issueColumn = IssueColumn::class;
	public string $valueType = self::VALUE_TYPE_NAME;

	public bool $withIssue = true;
	public bool $withCustomer = true;
	public bool $withCaption = false;
	public bool $withCustomerPhone = true;
	public bool $withContractor = true;
	public bool $withOwner = true;
	public bool $withUpdatedAt = true;

	public function init(): void {
		if ($this->filterModel !== null && !$this->filterModel instanceof SummonSearch) {
			throw new InvalidConfigException('$filterModel must be instance of: ' . SummonSearch::class . '.');
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

		parent::init();
	}

	public function defaultColumns(): array {
		return [
			[
				'class' => $this->issueColumn,
				'visible' => $this->withIssue,
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
			],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => SummonSearch::getStatusesNames(),
			],
			[
				'attribute' => 'title',
				'contentOptions' => ['style' => 'width: 35%;'],
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
			],
			[
				'attribute' => 'updated_at',
				'format' => 'date',
				'visible' => $this->withUpdatedAt,
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
				'filter' => SummonSearch::getContractorsNames(),
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
