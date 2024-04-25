<?php

namespace common\modules\lead\models\forms;

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadCost;
use yii\base\Model;

class LeadCostForm extends Model {

	public const SCENARIO_USER = 'user';

	public ?int $userId = null;

	public string $value = '';
	public string $date_at = '';
	public ?int $campaign_id = null;

	private ?LeadCost $model = null;
	private array $campaignNames = [];

	public function rules(): array {
		return [
			[['!userId'], 'required', 'on' => static::SCENARIO_USER],
			[['value', 'date_at', 'campaign_id'], 'required'],
			[['campaign_id'], 'integer'],
			[['value'], 'number', 'min' => 0],
			[['campaign_id'], 'in', 'range' => array_keys($this->getCampaignNames())],
		];
	}

	public function attributeLabels(): array {
		return LeadCost::instance()->attributeLabels();
	}

	public function getCampaignNames(): array {
		if (empty($this->campaignNames)) {
			$query = LeadCampaign::find();
			$query->andFilterWhere(['is_active' => true]);
			if (!$this->getModel()->isNewRecord) {
				$query->orWhere([LeadCampaign::tableName() . '.id' => $this->getModel()->campaign_id]);
			}
			if ($this->scenario === static::SCENARIO_USER) {
				$query->andWhere([LeadCampaign::tableName() . '.owner_id' => $this->userId]);
			} else {
				$query->joinWith('owner.userProfile');
			}
			$models = $query->all();
			$this->campaignNames = ArrayHelper::map(
				$models,
				'id',
				$this->scenario === static::SCENARIO_USER
					? 'name' : 'nameWithOwner'
			);
		}
		return $this->campaignNames;
	}

	public function getModel(): LeadCost {
		if ($this->model === null) {
			$this->model = new LeadCost();
		}
		return $this->model;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->campaign_id = $this->campaign_id;
		$model->date_at = $this->date_at;
		$model->value = $this->value;
		return $model->save(false);
	}

	public function setModel(LeadCost $model): void {
		$this->model = $model;
		$this->campaign_id = $model->campaign_id;
		$this->date_at = $model->date_at;
		$this->value = $model->value;
	}
}
