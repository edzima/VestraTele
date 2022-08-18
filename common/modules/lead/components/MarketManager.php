<?php

namespace common\modules\lead\components;

use common\modules\lead\models\forms\LeadMarketAccessResponseForm;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\base\Component;

class MarketManager extends Component {

	/**
	 * @param int[] $users users IDs indexed by LeadUser::$type.
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

	public function getFirstWaitingUser(LeadMarket $market): ?LeadMarketUser {
		$waiting = array_filter($market->leadMarketUsers, static function (LeadMarketUser $user) {
			return $user->isWaiting();
		});
		if (empty($waiting)) {
			return null;
		}
		usort($waiting, function (LeadMarketUser $a, LeadMarketUser $b) {
			return strtotime($a->updated_at) - strtotime($b->updated_at);
		});
		return reset($waiting);
	}

	public function expiredRenew(): ?int {
		$models = LeadMarket::find()
			->andWhere([
				LeadMarket::tableName() . '.status' => LeadMarket::STATUS_BOOKED,
			])
			->joinWith('leadMarketUsers')
			->all();

		$count = 0;
		foreach ($models as $model) {
			$count += $this->expireProcess($model);
		}

		return $count;
	}

	public function expireProcess(LeadMarket $market): ?int {
		if (!$market->isBooked() || empty($market->leadMarketUsers)) {
			return null;
		}
		$expired = 0;
		$hasAcceptedNotExpiredYet = false;
		foreach ($market->leadMarketUsers as $marketUser) {
			if ($marketUser->isAccepted()) {
				if ($marketUser->isExpired()) {
					$marketUser->updateAttributes([
						'status' => LeadMarketUser::STATUS_NOT_REALIZED,
					]);
					$expired++;
				} else {
					$hasAcceptedNotExpiredYet = true;
				}
			}
		}
		if (!$hasAcceptedNotExpiredYet) {
			$waitingUser = $this->getFirstWaitingUser($market);
			if ($waitingUser !== null) {
				$response = new LeadMarketAccessResponseForm($waitingUser);
				if ($response->accept()) {
					$response->sendAcceptEmail();
				}
			} else {
				$this->sendEmailAboutToConfirmsUsers($market);
			}
		}

		return $expired;
	}

	public function sendEmailAboutToConfirmsUsers(LeadMarket $market): bool {
		$emails = $this->getMarketEmails($market);
		if (empty($emails)) {
			return false;
		}
		$toConfirms = array_filter($market->leadMarketUsers, static function (LeadMarketUser $marketUser): bool {
			return $marketUser->isToConfirm();
		});
		if (empty($toConfirms)) {
			return false;
		}
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadMarketToConfirmsList-html', 'text' => 'leadMarketToConfirmsList-text'],
				[
					'model' => $market,
					'users' => $toConfirms,
				]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($emails)
			->setSubject(Yii::t('lead', '{count} Access Request to Confirm for Lead: {lead} from Market.', [
				'lead' => $market->lead->getName(),
				'count' => count($toConfirms),
			]))
			->send();
	}

	public function getMarketEmails(LeadMarket $market): array {
		$emails = [];
		$emails[] = $market->creator->getEmail();
		$owner = $market->lead->owner;
		if ($owner !== null
			&& $market->creator->getID() !== $owner->getID()
		) {
			$emails[] = $owner->getEmail();
		}
		return $emails;
	}

	public function sendLeadChangeStatusEmail(LeadMarket $market): bool {
		$emails = $this->getMarketEmails($market);
		if (empty($emails)) {
			return false;
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
