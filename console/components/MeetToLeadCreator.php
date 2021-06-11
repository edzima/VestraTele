<?php

namespace console\components;

use common\models\issue\IssueMeet;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use yii\base\Component;
use yii\helpers\Json;
use yii\helpers\StringHelper;

class MeetToLeadCreator extends Component {

	public string $sourceName = 'meets';
	public string $emptyCampaignName = 'Własna';

	private array $sources = [];

	public function createLead(IssueMeet $meet): LeadForm {
		$lead = new LeadForm();
		$lead->phone = $meet->phone;
		$lead->email = $meet->email;
		$lead->postal_code = $meet->customerAddress->postal_code ?? null;
		$lead->date_at = $meet->created_at;
		$lead->status_id = $this->getStatus($meet->getStatusName())->id;
		$lead->source_id = $this->getSource($meet)->id;
		$lead->campaign_id = $this->getCampaign($meet)->id;
		$lead->data = Json::encode($meet->getAttributes());
		$lead->owner_id = $meet->agent_id;
		$lead->tele_id = $meet->tele_id;
		return $lead;
	}

	public function createReport(ActiveLead $lead, IssueMeet $meet): ?ReportForm {
		if ($meet->agent_id === null && $meet->tele_id === null) {
			return null;
		}
		$model = new ReportForm();
		$model->setLead($lead);
		$model->owner_id = $meet->agent_id ? $meet->agent_id : $meet->tele_id;
		$model->address = $meet->customerAddress;
		$model->details = $meet->details;
		$model->getModel()->created_at = $meet->created_at;
		$model->getModel()->updated_at = $meet->updated_at;
		$answers = [];
		if ($meet->client_name) {
			$answers[1] = $meet->client_name;
		}
		if ($meet->client_surname) {
			$answers[2] = $meet->client_surname;
		}
		$model->setOpenAnswers($answers);
		return $model;
	}

	private function getSource(IssueMeet $issueMeet): LeadSource {
		$type = $this->getType($issueMeet->type->name);
		$type_id = $type->id;
		if (!isset($this->sources[$type_id])) {
			$sourceName = $this->sourceName . ' - ' . $type->name;
			$model = LeadSource::find()->andWhere(['name' => $sourceName])->one();
			if ($model === null) {
				$model = new LeadSource([
					'name' => $sourceName,
					'type_id' => $type_id,
				]);
				$model->save();
			}
			$this->sources[$type_id] = $model;
		}

		return $this->sources[$type_id];
	}

	private function getCampaign(IssueMeet $meet): LeadCampaign {
		$campaign = $meet->campaign;
		if ($campaign) {
			$name = $campaign->name;
			$models = LeadCampaign::getModels();
			foreach ($models as $model) {
				if ($model->name === $name) {
					return $model;
				}
			}
			$model = new LeadCampaign();
			$model->name = $campaign->name;
			$model->save();
			LeadCampaign::getModels(true);
			return $model;
		}
		$owner_id = $meet->agent_id ?: $meet->tele_id;
		$model = LeadCampaign::find()
			->andWhere(['name' => $this->emptyCampaignName, 'owner_id' => $owner_id])
			->one();
		if ($model) {
			return $model;
		}
		$model = new LeadCampaign();
		$model->name = $this->emptyCampaignName;
		$model->owner_id = $owner_id;
		$model->save();
		LeadCampaign::getModels(true);
		return $model;
	}

	private function getType(string $name): LeadType {
		if (StringHelper::startsWith($name, 'Świadcze')) {
			$name = 'Świadczenie pielegnacyjne';
		}

		$models = LeadType::getModels();
		foreach ($models as $model) {
			if ($model->name === $name) {
				return $model;
			}
		}
		$model = new LeadType(['name' => $name]);
		$model->save();
		LeadType::getModels(true);
		return $model;
	}

	private function getStatus(string $name): LeadStatus {
		$models = LeadStatus::getModels();
		foreach ($models as $model) {
			if ($model->name === $name) {
				return $model;
			}
		}
		$model = new LeadStatus(['name' => $name]);
		$model->save();
		LeadStatus::getModels(true);
		return $model;
	}

}
