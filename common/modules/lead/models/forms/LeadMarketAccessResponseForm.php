<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\base\Model;

class LeadMarketAccessResponseForm extends Model {

	private LeadMarketUser $model;

	public function __construct(LeadMarketUser $marketUser, $config = []) {
		$this->model = $marketUser;
		parent::__construct($config);
	}

	public function accept(bool $checkActiveReservation = true): ?string {
		if ($this->userAlreadyInLead()) {
			if ($this->model->isToConfirm()) {
				Yii::warning([
					'message' => 'User is Already in Lead',
					'market_id' => $this->model->market_id,
					'user_id' => $this->model->user_id,
				]);
				$this->model->delete();
			}
			return null;
		}
		if ($checkActiveReservation && $this->model->market->hasActiveReservation()) {
			$this->model->status = LeadMarketUser::STATUS_WAITING;
			$this->model->updateAttributes(['status']);
			return null;
		}

		$type = $this->linkUserToLead();
		if ($type === null) {
			$this->model->market->status = LeadMarket::STATUS_USERS_COUNT_LIMIT_EXCEED;
			$this->model->market->updateAttributes(['status']);
			return null;
		}
		$this->model->status = LeadMarketUser::STATUS_ACCEPTED;
		$this->model->generateReservedAt();
		$this->model->market->status = LeadMarket::STATUS_BOOKED;
		$this->model->updateAttributes([
			'status',
			'reserved_at',
		]);
		$this->model->market->updateAttributes(['status']);
		return $type;
	}

	public function linkUserToLead(): ?string {
		if ($this->userAlreadyInLead()) {
			Yii::warning('Try add User: ' . $this->model->user_id .
				' from Market: ' . $this->model->market_id . ' who already in Lead.',
				'lead.market.user'
			);
			return null;
		}
		$type = $this->getLeadUserMarketTypeToAssign();
		if ($type) {
			$this->model->market->lead->linkUser($type, $this->model->user_id);
			return $type;
		}
		return null;
	}

	public function userAlreadyInLead(): bool {
		return $this->model->market->lead->isForUser($this->model->user_id);
	}

	private function getLeadUserMarketTypeToAssign(): ?string {
		$marketUsersCount = count(array_filter($this->model->market->lead->leadUsers, static function (LeadUser $leadUser): bool {
			return $leadUser->isMarketType();
		}));
		switch ($marketUsersCount) {
			case 0:
				return LeadUser::TYPE_MARKET_FIRST;
			case 1:
				return LeadUser::TYPE_MARKET_SECOND;
			case 2:
				return LeadUser::TYPE_MARKET_THIRD;
		}
		Yii::warning([
			'message' => 'Invalid Market Users Count to Assign',
			'market_id' => $this->model->market_id,
			'user_id' => $this->model->user_id,
			'leadUsers' => $this->model->market->lead->getUsers(),
			'marketUsersCount' => $marketUsersCount,
		], 'lead.market.user');
		return null;
	}

	public static function getLinkedUserTypeName(string $type): string {
		return LeadUser::getTypesNames()[$type];
	}

	public function sendAcceptEmail(): bool {
		if (!$this->model->isAccepted()) {
			return false;
		}
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadMarketUserAccessResponse-html', 'text' => 'leadMarketUserAccessResponse-text'],
				[
					'model' => $this->model,
				]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::t('lead', 'Market Leads')])
			->setTo($this->model->user->getEmail())
			->setSubject(Yii::t('lead', 'Your Access Request is Accepted.'))
			->send();
	}

	public function sendRejectEmail(): bool {
		if (!$this->model->isRejected()) {
			return false;
		}
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadMarketUserAccessResponse-html', 'text' => 'leadMarketUserAccessResponse-text'],
				[
					'model' => $this->model,
				]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($this->model->user->getEmail())
			->setSubject(Yii::t('lead', 'Your Access Request is Rejected.'))
			->send();
	}

	public function sendWaitingEmail(): bool {
		if (!$this->model->isWaiting()) {
			return false;
		}
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadMarketUserAccessResponse-html', 'text' => 'leadMarketUserAccessResponse-text'],
				[
					'model' => $this->model,
				]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($this->model->user->getEmail())
			->setSubject(Yii::t('lead', 'Your Access Request is Waiting.'))
			->send();
	}

	public function reject(): void {
		$this->model->status = LeadMarketUser::STATUS_REJECTED;
		$this->model->reserved_at = null;

		$this->model->updateAttributes([
			'status',
			'reserved_at',
		]);
	}

	public function giveUp(): void {
		$this->model->status = LeadMarketUser::STATUS_GIVEN_UP;
		$this->model->reserved_at = null;
		$this->model->updateAttributes([
			'status',
			'reserved_at',
		]);
	}

}
