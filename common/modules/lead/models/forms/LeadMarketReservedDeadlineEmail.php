<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use Yii;
use yii\base\Model;
use yii\db\Expression;

class LeadMarketReservedDeadlineEmail extends Model {

	public $days;

	public function rules(): array {
		return [
			['days', 'required'],
			['days', 'integer', 'min' => 0],
		];
	}

	public function sendEmail(LeadMarketUser $model): bool {
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadMarketReservedDeadline-html', 'text' => 'leadMarketReservedDeadline-text'],
				['model' => $model]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($model->user->getEmail())
			->setSubject($this->getSubject())
			->send();
	}

	protected function getSubject(): string {
		return Yii::t('lead', '{n,plural,=0{Today} =1{Tomorrow} other{In # days}} you will lose access to the Lead from Market.', [
			'n' => $this->days,
		]);
	}

	public function sendEmails(): ?int {
		if (!$this->validate()) {
			return null;
		}
		$models = $this->findModels();
		if (empty($models)) {
			Yii::warning('Not find models', __METHOD__);
			return null;
		}

		$count = 0;
		foreach ($models as $model) {
			if ($this->sendEmail($model)) {
				Yii::warning('Send Email', __METHOD__);
				$count++;
			}
		}
		return $count;
	}

	/**
	 * @return LeadMarketUser[]
	 */
	private function findModels(): array {
		return LeadMarketUser::find()
			->joinWith('market')
			->with('user')
			->andWhere(['=', new Expression('DATEDIFF(CURDATE(), reserved_at)'), -($this->days)])
			->andWhere([LeadMarket::tableName() . '.status' => LeadMarket::STATUS_BOOKED])
			->all();
	}

}
