<?php

namespace frontend\models;

use common\models\issue\Summon;
use common\models\issue\SummonType;
use frontend\models\search\SummonSearch;
use yii\data\ActiveDataProvider;

class ContactorSummonCalendarSearch extends SummonSearch {

	public string $start;
	public string $end;
	public int $contractor_id;
	public int $owner_id;
	public int $typeId;
	public int $title;

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search(array $params = []): ActiveDataProvider {
		$query = Summon::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['updated_at' => SORT_DESC],
			],
			'pagination' => false,
		]);

		$query->andWhere(['contractor_id' => $this->contractor_id])
			->andWhere(['>=', 'start_at', $this->start])
			->andWhere(['<=', 'start_at', $this->end]);

		return $dataProvider;
	}

	public const STATUS_FILTERS = [
		self::STATUS_NEW => [
			'color' => '#d32f2f',
		],
		self::STATUS_IN_PROGRESS => [
			'color' => '#C2185B',
		],
		self::STATUS_WITHOUT_RECOGNITION => [
			'color' => '#7B1FA2',
		],
		self::STATUS_TO_CONFIRM => [
			'color' => '#FF9100',
		],
		self::STATUS_REALIZED => [
			'color' => '#303F9F',
		],
		self::STATUS_UNREALIZED => [
			'color' => '#616161',
		],
	];
//
//	public const TYPE_FILTERS = [
//		self::TYPE_APPEAL => [
//			'color' => '#E64A19',
//		],
//		self::TYPE_INCOMPLETE_DOCUMENTATION => [
//			'color' => '#0097A7',
//		],
//		self::TYPE_PHONE => [
//			'color' => '#00796B',
//		],
//		self::TYPE_ANTIVINDICATION => [
//			'color' => '#388E3C',
//		],
//		self::TYPE_URGENCY => [
//			'color' => '#689F38',
//		],
//		self::TYPE_RESIGNATION => [
//			'color' => '#AFB42B',
//		],
//	];

	public static function getStatusFiltersOptions(): array {
		$options = [];
		$statusNames = Summon::getStatusesNames();
		foreach (static::STATUS_FILTERS as $status => $filter) {
			$options[] = [
				'value' => $status,
				'isActive' => true,
				'label' => $statusNames[$status],
				'color' => $filter['color'],
				'eventColors' => [
					'background' => $filter['color'],
				],
			];
		}
		return $options;
	}

	public static function getTypesFilterOptions(): array {
		$options = [];
		$typesNames = SummonType::getNames();

		$colors = [
			'#E64A19', '#0097A7', '#00796B', '#388E3C', '#689F38', '#AFB42B',
		];

		foreach ($typesNames as $id => $name) {
			$color = $colors[array_rand($colors)];
			$options[] = [
				'value' => $id,
				'isActive' => true,
				'label' => $name,
				'color' => $color,
				'eventColors' => [
					'badge' => $color,
				],
			];
		}
		return $options;
	}

	public static function getKindFilterOptions(): array {
		$options = [];
		$options[] = [
			'value' => 'event',
			'isActive' => true,
			'label' => 'wezwanie',
			'color' => '#2196F3',
			'eventColors' => [
				'outline' => '#2196F3',
			],
		];
		$options[] = [
			'value' => 'deadline',
			'isActive' => true,
			'label' => 'deadline',
			'color' => '#F44336',
			'eventColors' => [
				'border' => '#F44336',
				'background' => '#000000',
			],
		];
		return $options;
	}
}
