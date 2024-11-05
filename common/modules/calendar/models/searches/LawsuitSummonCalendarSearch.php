<?php

namespace common\modules\calendar\models\searches;

use common\helpers\Html;
use common\models\issue\query\SummonQuery;
use common\models\issue\search\SummonSearch;
use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\modules\calendar\models\LawsuitSummonCalendarEvent;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class LawsuitSummonCalendarSearch extends SummonSearch {

	public $start;
	public $end;

	private ?array $types = null;

	public function rules(): array {
		return [
			[['start', 'end'], 'required'],
		];
	}

	public function search(array $params = []): ActiveDataProvider {
		$query = Summon::find();
		$query->with([
			'issue.customer.userProfile',
			'issue.entityResponsible',
			'type',
		]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['updated_at' => SORT_DESC],
			],
			'pagination' => false,
		]);
		$typesIds = array_keys($this->getSummonsTypes());
		if (!$this->validate() || empty($typesIds)) {
			$query->andWhere('0=1');
			return $dataProvider;
		}
		$query->andFilterWhere([Summon::tableName() . '.contractor_id' => $this->contractor_id]);
		$query->andWhere([Summon::tableName() . '.type_id' => $typesIds]);
		$this->applyDateFilter($query);

		return $dataProvider;
	}

	private function applyDateFilter(SummonQuery $query) {
		$query->andWhere(['>=', Summon::tableName() . '.deadline_at', $this->start])
			->andWhere(['<=', Summon::tableName() . '.deadline_at', $this->end]);
	}

	public function getEventsData(array $config = []): array {
		$data = [];
		foreach ($this->search()->getModels() as $model) {
			/** @var Summon $model */
			$event = new LawsuitSummonCalendarEvent($config);
			$event->setModel($model);
			$data[] = $event->toArray();
		}
		return $data;
	}

	public function getSummonsTypesFilters(): array {
		$types = $this->getSummonsTypes();
		$filters = [];
		foreach ($types as $type) {
			$filters[] = [
				'value' => $type->id,
				'label' => Html::encode($type->name),
				'isActive' => true,
				'color' => $type->getOptions()->lawsuitCalendarBackground,
			];
		}
		return $filters;
	}

	/**
	 * @return SummonType[]
	 */
	protected function getSummonsTypes(): array {
		if ($this->types === null) {
			$this->types = SummonType::find()
				->andWhere(new Expression("JSON_EXISTS(options, '$.lawsuitCalendarBackground')"))
				->indexBy('id')
				->all();
		}
		return $this->types;
	}

}
