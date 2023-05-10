<?php

namespace common\modules\lead\models\forms;

use common\models\KeyStorageItem;
use common\models\user\Customer;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;
use Yii;
use yii\helpers\Json;

class CustomerLeadForm extends LeadForm {

	public $status_id = LeadStatusInterface::STATUS_NEW;
	public $provider = Lead::PROVIDER_CRM_CUSTOMER;

	public function load($data, $formName = ''): bool {
		return parent::load($data, $formName);
	}

	public static function customerAttributes(Customer $model): ?array {
		if (empty($model->email) && empty($model->getPhone())) {
			return null;
		}
		$source = (int) Yii::$app->keyStorage->get(KeyStorageItem::KEY_LEAD_CUSTOMER_SOURCE);
		if (empty($source)) {
			return null;
		}
		$lead = new static();
		$lead->email = $model->email;
		$lead->phone = $model->getPhone();
		$lead->name = $model->profile->firstname . ' ' . $model->profile->lastname;
		$lead->date_at = date($lead->dateFormat, $model->created_at);
		$lead->data = Json::encode([
			'customerUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/customer/view', 'id' => $model->id]),
		]);
		$lead->source_id = $source;
		return $lead->getAttributes();
	}

}
