<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadInterface;
use Yii;
use yii\base\Model;

class LeadPushEmail extends Model {

	public ?string $email = null;

	public function rules(): array {
		return [
			['email', 'trim'],
			['email', 'required'],
			['email', 'email'],
		];
	}

	private LeadInterface $lead;

	public function __construct(LeadInterface $lead, $config = []) {
		$this->lead = $lead;
		parent::__construct($config);
	}

	public function sendEmail(): bool {
		return Yii::$app
			->mailer
			->compose(
				['html' => 'leadPush-html', 'text' => 'leadPush-text'],
				['lead' => $this->lead]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
			->setTo($this->email)
			->setSubject(Yii::t('lead', 'Push new Lead to ' . Yii::$app->name))
			->send();
	}
}
