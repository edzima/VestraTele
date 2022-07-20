<?php

namespace common\modules\lead\components;

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadUser;
use yii\base\Component;

class MarketManager extends Component {

	/**
	 * @param int[] $users users IDs indexed by type.
	 * @param int $userId
	 * @return bool
	 */
	public function isFromMarket(array $users, int $userId): bool {
		$types = LeadUser::marketTypes();
		foreach ($users as $type => $id) {
			if ($id === $userId && in_array($type, $types, true)) {
				return true;
			}
		}
		return false;
	}

	public function hasExpiredReservation(int $leadId, int $userId): ?bool {
		$model = LeadMarketUser::find()
			->joinWith('market')
			->andWhere([
				'user_id' => $userId,
				LeadMarket::tableName() . '.lead_id' => $leadId,
			])
			->one();

		return $model?->isExpired();
	}
}
