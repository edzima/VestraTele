<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class LeadMarketAccessRequest extends Model {

	public const DEFAULT_DAYS = 2;

	public $user_id;
	public string $details = '';
	public int $days = self::DEFAULT_DAYS;

	private LeadMarket $market;
	private ?LeadMarketUser $model = null;

	public function rules(): array {
		return [
			[['!user_id', 'days'], 'required'],
			[['user_id', 'days'], 'integer', 'min' => 1],
			[
				'details', 'required',
				'when' => function (): bool {
					return $this->days !== static::DEFAULT_DAYS;
				},
				'message' => Yii::t('lead', 'Details cannot be blank when Days is other than: {defaultDays}.', [
					'defaultDays' => static::DEFAULT_DAYS,
				]),
				'enableClientValidation' => false,
			],
		];
	}

	public function attributeLabels() {
		return [
			'details' => Yii::t('lead', 'Details'),
			'days' => Yii::t('lead', 'How many Days'),
		];
	}

	public function getMarket(): LeadMarket {
		return $this->market;
	}

	public function getModel(): LeadMarketUser {
		if ($this->model === null) {
			$this->model = $this->market->leadMarketUsers[$this->user_id] ?? new LeadMarketUser();
		}
		return $this->model;
	}

	public function setModel(LeadMarketUser $leadMarketUser): void {
		$this->setMarket($leadMarketUser->market);
		$this->model = $leadMarketUser;
		$this->user_id = $leadMarketUser->user_id;
		$this->days = $leadMarketUser->days_reservation;
	}

	/**
	 * @param LeadMarket $market
	 * @return void
	 */
	public function setMarket(LeadMarket $market): void {
		if ($market->isArchived()) {
			throw new InvalidArgumentException(Yii::t('lead', 'Market cannot be Archived.'));
		}
		if ($market->isDone()) {
			throw new InvalidArgumentException(Yii::t('lead', 'Market cannot be Done.'));
		}
		$this->market = $market;
	}

	protected function sendEmail(): bool {
		$owner = $this->market->lead->owner;
		$emails = [$this->market->creator->getEmail()];
		if ($owner !== null && $owner->getID() !== $this->market->creator_id) {
			$emails[] = $this->market->creator->getEmail();
		}
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadMarketAccessRequest-html', 'text' => 'leadMarketAccessRequest-text'],
				['model' => $this->getModel()]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($emails)
			->setSubject(Yii::t('lead', '{user} Access request for Lead: {lead} Market: {status}', [
				'user' => $this->getModel()->user->getFullName(),
				'lead' => $this->market->lead->getName(),
				'status' => $this->market->getStatusName(),
			]))
			->send();
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$isNewRecord = $model->isNewRecord;
		if ($isNewRecord) {
			$model->status = LeadMarketUser::STATUS_TO_CONFIRM;
		}
		$model->market_id = $this->market->id;
		$model->user_id = $this->user_id;
		$model->details = $this->details;
		$model->days_reservation = $this->days;
		if ($model->save()) {
			if ($isNewRecord) {
				$this->sendEmail();
			}
			return true;
		}
		return false;
	}

}
