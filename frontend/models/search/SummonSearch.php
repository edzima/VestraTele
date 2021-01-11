<?php

namespace frontend\models\search;

use common\models\issue\search\SummonSearch as BaseSummonSearch;
use common\models\user\CustomerSearchInterface;
use yii\data\ActiveDataProvider;

/**
 * Summon search model for frontend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonSearch extends BaseSummonSearch {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type', 'status', 'term', 'created_at', 'updated_at', 'realized_at', 'start_at', 'issue_id', 'owner_id'], 'integer'],
			[['title'], 'safe'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		if (empty($this->status)) {
			$provider->query->andWhere([
				static::SUMMON_ALIAS . '.status' => array_keys(static::getActiveStatusesNames()),
			]);
		}
		return $provider;
	}

	public static function getActiveStatusesNames(): array {
		$statuses = static::getStatusesNames();
		unset($statuses[static::STATUS_REALIZED], $statuses[static::STATUS_UNREALIZED]);
		return $statuses;
	}
}
