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

		return $model
			? $model->isExpired()
			: null;
	}

	public function expiredRenew(): ?int {
		$models = LeadMarket::find()
			->andWhere([
				LeadMarket::tableName() . '.status' => LeadMarket::STATUS_BOOKED,
			])
			->joinWith('leadMarketUsers')
			->andWhere([
				LeadMarketUser::tableName() . '.status' => LeadMarketUser::STATUS_ACCEPTED,
			])
			->all();

		$ids = [];
		foreach ($models as $model) {
			$expired = array_filter($model->leadMarketUsers, static function (LeadMarketUser $marketUser): bool {
				return $marketUser->isExpired();
			});
			if (count($expired) === count($model->leadMarketUsers)) {
				$ids[] = $model->id;
			}
		}

		if (!empty($ids)) {
			return LeadMarket::updateAll([
				'status' => LeadMarket::STATUS_AVAILABLE_AGAIN,
			], [
				'id' => $ids,
			]);
		}
		return null;
	}

	public function accept(int $marketId, int $userId): void {
		$model = LeadMarketUser::find()
			->andWhere([
				'market_id' => $marketId,
				'user_id' => $userId,
			])->one();
		if ($model !== null) {
			$model->status = LeadMarketUser::STATUS_ACCEPTED;
			$model->reserved_at = $model->generateReservedAt();
			$model->save(false);
			$model->market->status = LeadMarket::STATUS_BOOKED;
			$model->market->bookIt();
		}
	}
}
