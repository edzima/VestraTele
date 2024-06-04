<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use Yii;
use yii\base\Model;

class LeadCampaignForm extends Model {

	public const SCENARIO_OWNER = 'owner';
	public array $leadsIds = [];

	public ?int $ownerId = null;

	public bool $active = true;
	public ?int $campaignId = null;

	public function rules(): array {
		return [
			[['!ownerId', 'leadsIds'], 'required', 'on' => static::SCENARIO_OWNER],
			[['campaignId'], 'integer'],
			['campaignId', 'in', 'range' => array_keys($this->getCampaignNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'ownerId' => Yii::t('lead', 'Owner'),
			'campaignId' => Yii::t('lead', 'Campaign'),
		];
	}

	public function getCampaignNames(): array {
		return LeadCampaign::getNames($this->ownerId, $this->active);
	}

	public function save(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}
		$leadsIds = $this->getLeadsIds();
		if (empty($leadsIds)) {
			return null;
		}

		return Lead::updateAll(['campaign_id' => $this->campaignId], [
			'id' => $leadsIds,
		]);
	}

	public function getLeadsIds() {
		$query = Lead::find();
		$query->select('id');
		$query->andWhere(['id' => $this->leadsIds]);
		if ($this->ownerId) {
			$query->owner($this->ownerId);
		}
		return $query->column();
	}
}
