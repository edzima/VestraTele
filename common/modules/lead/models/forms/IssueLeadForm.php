<?php

namespace common\modules\lead\models\forms;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;
use Yii;
use yii\helpers\Json;

class IssueLeadForm extends LeadForm {

	public $status_id = LeadStatusInterface::STATUS_NEW;
	public $provider = Lead::PROVIDER_CRM_CUSTOMER;

	public function load($data, $formName = ''): bool {
		return parent::load($data, $formName);
	}

	public static function issueCustomerAttributes(Issue $model): ?array {
		$customer = $model->customer;
		if (empty($customer->email) && empty($customer->getPhone())) {
			Yii::warning('Customer: ' . $customer->id . ' without email and phone in Issue: ' . $model->getIssueName(), __METHOD__);
			return null;
		}
		$sourceId = static::getLeadSourceId($model->type);
		if ($sourceId === null) {
			return null;
		}
		if (!static::isFirstCustomerIssueOfType($model)) {
			return null;
		}
		$lead = new static();
		$lead->email = $customer->email;
		$lead->phone = $customer->getPhone();
		$lead->name = $customer->getFullName() . ' - ' . $model->getIssueName();
		$lead->date_at = date($lead->dateFormat, is_string($model->created_at) ? strtotime($model->created_at) : null);
		$lead->data = Json::encode([
			'customerUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/customer/view', 'id' => $customer->id]),
		]);
		$lead->source_id = $sourceId;
		return $lead->getAttributes();
	}

	protected static function getLeadSourceId(IssueType $type): ?int {
		if ($type->lead_source_id) {
			return $type->lead_source_id;
		}
		if ($type->parent_id !== null && $type->parent !== null) {
			return static::getLeadSourceId($type->parent);
		}
		return null;
	}

	protected static function isFirstCustomerIssueOfType(Issue $model): bool {
		$count = (int) IssueUser::find()
			->andWhere(['user_id' => $model->customer->id])
			->withType(IssueUser::TYPE_CUSTOMER)
			->joinWith('issue.type')
			->andWhere([IssueType::tableName() . '.id' => static::getTypesIds($model->type)])
			->count();
		return $count === 1;
	}

	protected static function getTypesIds(IssueType $type): array {
		if ($type->parent_id === null || $type->parent === null) {
			return [$type->id];
		}
		return ArrayHelper::getColumn($type->parent->childs, 'id');
	}

}
