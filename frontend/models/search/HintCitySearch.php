<?php

namespace frontend\models\search;

use common\models\hint\searches\HintCitySearch as BaseHintSearch;
use yii\base\InvalidConfigException;
use yii\data\DataProviderInterface;

class HintCitySearch extends BaseHintSearch {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['city_id'], 'integer'],
			[['type', 'status', 'details', 'cityName'], 'safe'],
		];
	}

	/**
	 * @param array $params
	 * @return DataProviderInterface
	 * @throws InvalidConfigException
	 */
	public function search(array $params): DataProviderInterface {
		if (empty($this->user_id)) {
			throw new InvalidConfigException('User Id must be set.');
		}
		return parent::search($params);
	}
}
