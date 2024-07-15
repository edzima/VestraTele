<?php

namespace common\modules\lead\entities;

use common\modules\lead\models\LeadCampaign;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Model;

class FBCampaign extends Model {

	public string $campaignId = '';
	public string $campaignName = '';
	public string $adsetId = '';
	public string $adsetName = '';

	public string $adId = '';
	public string $adName = '';

	/**
	 * @var LeadCampaign[]
	 */
	private array $models = [];
	public bool $createCampaigns = false;

	public function rules(): array {
		return [
			[['adId'], 'required'],
			[['campaignId', 'campaignName', 'adsetId', 'adsetName', 'adId', 'adName'], 'string'],
		];
	}

	public function getLeadCampaign(string $type = LeadCampaign::TYPE_AD): ?LeadCampaign {
		$entityId = $this->getEntityId($type);
		$model = $this->findEntity($type, $entityId);
		if ($model) {
			return $model;
		}
		if (!$this->createCampaigns) {
			return null;
		}

		$model = new LeadCampaign();
		$model->type = $type;
		$model->entity_id = $entityId;
		$model->name = $this->getEntityName($type);
		switch ($type) {
			case LeadCampaign::TYPE_AD:
				$model->parent_id = $this->getLeadCampaign(LeadCampaign::TYPE_ADSET)->id;
				break;
			case LeadCampaign::TYPE_ADSET:
				$model->parent_id = $this->getLeadCampaign(LeadCampaign::TYPE_CAMPAIGN)->id;
				break;
		}
		if ($model->save()) {
			return $model;
		}
		Yii::error($model->getErrors(), __METHOD__);
		throw new InvalidConfigException('Problem with Model save.');
	}

	protected function findEntity(string $type, string $entityId): ?LeadCampaign {
		$key = $type . '_' . $entityId;
		if (!isset($this->models[$key])) {
			$this->models[$key] = LeadCampaign::find()
				->andWhere([
					'type' => $type,
					'entity_id' => $entityId,
				])
				->one();
		}
		return $this->models[$key];
	}

	private function getEntityId(string $type): string {
		switch ($type) {
			case LeadCampaign::TYPE_AD:
				return $this->adId;
			case LeadCampaign::TYPE_ADSET:
				return $this->adsetId;
			case LeadCampaign::TYPE_CAMPAIGN:
				return $this->campaignId;
		}
		throw new InvalidArgumentException('Invalid type: ' . $type);
	}

	private function getEntityName(string $type): string {
		switch ($type) {
			case LeadCampaign::TYPE_AD:
				return $this->adName;
			case LeadCampaign::TYPE_ADSET:
				return $this->adsetName;
			case LeadCampaign::TYPE_CAMPAIGN:
				return $this->campaignName;
		}
		throw new InvalidArgumentException('Invalid type: ' . $type);
	}
}
