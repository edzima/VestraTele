<?php

namespace common\widgets\grid;

use common\models\issue\search\SummonSearch;
use common\widgets\GridView;
use kartik\select2\Select2;
use Yii;

class SummonGrid extends GridView {

	public $id = 'summon-grid';

	/** @todo add note link */
	public $actionColumn = [
		'class' => ActionColumn::class,
		'template' => '{note} {view} {update} {delete}',
	];

	public string $issueColumn = IssueColumn::class;

	public bool $withIssue = true;
	public bool $withCustomer = true;
	public bool $withCaption = false;
	public bool $withContractor = true;
	public bool $withOwner = true;
	public bool $withUpdatedAt = true;

	public function init(): void {
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
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => SummonSearch::getTypesNames(),
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
				'filter' => SummonSearch::getOwnersNames(),
				'filterType' => static::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => Yii::t('common', 'Owner'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
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
				],
				'visible' => $this->withContractor,
			],
			$this->actionColumn,
		];
	}

}
