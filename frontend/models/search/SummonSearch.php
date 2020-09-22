<?php

namespace frontend\models\search;

use common\models\issue\search\SummonSearch as BaseSummonSearch;
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
			[['id', 'type', 'status', 'term', 'updated_at', 'realized_at', 'start_at', 'issue_id', 'owner_id'], 'integer'],
			[['title'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		if (empty($this->status)) {
			$provider->query->andWhere(['status' => array_keys(static::getActiveStatusesNames())]);
		}
		return $provider;
	}

	public static function getActiveStatusesNames(): array {
		$statuses = static::getStatusesNames();
		unset($statuses[static::STATUS_REALIZED], $statuses[static::STATUS_UNREALIZED]);
		return $statuses;
	}
}
