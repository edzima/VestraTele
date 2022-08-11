<?php

namespace common\modules\lead\components;

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadUser;
use Yii;
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

	public function sendLeadChangeStatus(LeadMarket $market): bool {
		$emails = [];
		$emails[] = $market->creator->getEmail();
		if ($market->lead->owner) {
			$emails[] = $market->lead->owner->getEmail();
		}
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadFromMarketChangeStatus-html', 'text' => 'leadFromMarketChangeStatus-text'],
				[
					'model' => $market,
				]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($emails)
			->setSubject(Yii::t('lead', 'Lead: {lead} from Market change Status: {status}.', [
				'lead' => $market->lead->getName(),
				'status' => $market->lead->getStatusName(),
			]))
			->send();
	}

}
