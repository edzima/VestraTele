<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\ActiveLead;
use common\widgets\GridView;
use kartik\grid\ExpandRowColumn;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;

class SameContactsGridView extends GridView {

	public ?ActiveLead $model = null;
	public bool $withType = false;
	public $showOnEmpty = false;
	public $emptyText = false;
	public $summary = false;

	public function init(): void {
		if ($this->dataProvider === null) {
			if ($this->model === null) {
				throw new InvalidConfigException('$model must be set when dataProvider is not set.');
			}
			$this->dataProvider = new ArrayDataProvider([
				'allModels' => $this->getModels(),
				'key' => 'id',
				'sort' => [
					'attributes' => [
						'date_at',
						'typeName',
					],
					'defaultOrder' => [
						'date_at' => SORT_DESC,
					],
				],
			]);
		}
		$this->caption = Yii::t('lead', 'Same Contacts Leads');
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		parent::init();
	}

	private function getModels(): array {
		return $this->model->getSameContacts($this->withType);
	}

	protected function defaultColumns(): array {
		return [
			'typeName',
			'statusName',
			'date_at:datetime',
			'sourceName',
			'owner',
			[
				'class' => ExpandRowColumn::class,
				'detailUrl' => [
					'detailReport',
				],
				'value' => function () {
					return GridView::ROW_COLLAPSED;
				},
			],
		];
	}
}
