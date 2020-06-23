<?php

namespace frontend\models;

use common\models\issue\IssueMeet;
use yii\data\ActiveDataProvider;

class AgentMeetCalendarSearch extends IssueMeetSearch {

	public $start;
	public $end;

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search(array $params = []): ActiveDataProvider {
		$query = IssueMeet::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['updated_at' => SORT_DESC],
			],
			'pagination' => false,
		]);

		$query->andWhere(['agent_id' => $this->agent_id])
			->andWhere([' >= ', 'date_at', $this->start])
			->andWhere([' <= ', 'date_at', $this->end])
			->andWhere(['status' => array_keys(static::STATUS_FILTERS)]);

		return $dataProvider;
	}

	public const STATUS_FILTERS = [
		self::STATUS_NEW => [
			'color' => 'orange',
		],
		self::STATUS_ESTABLISHED => [
			'color' => 'blue',
		],
		self::STATUS_SIGNED_CONTRACT => [
			'color' => 'green',
		],
		self::STATUS_NOT_SIGNED => [
			'color' => 'purple',
		],
		self::STATUS_CONTACT_AGAIN => [
			'color' => 'red',
		],
	];

	public static function getFiltersOptions(): array {
		$names = static::getStatusNames();
		$options = [];
		foreach (static::STATUS_FILTERS as $status => $itemOptions) {
			$options[] = [
				'id' => $status,
				'isActive' => true,
				'label' => $names[$status],
				'itemOptions' => $itemOptions,
			];
		}
		return $options;
	}

}
