<?php

namespace frontend\models\search;

use common\models\hint\searches\HintCitySourceSearch as BaseHintCitySourceSearch;
use yii\base\InvalidConfigException;
use yii\data\DataProviderInterface;

class HintCitySourceSearch extends BaseHintCitySourceSearch {

	public function rules(): array {
		return [
			[['source_id'], 'integer'],
			[['rating', 'phone', 'hintType', 'hintStatus', 'hintCityName'], 'string'],
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
