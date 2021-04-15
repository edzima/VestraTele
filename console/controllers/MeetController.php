<?php

namespace console\controllers;

use common\models\issue\IssueMeet;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use Yii;
use yii\console\Controller;

class MeetController extends Controller {

	public string $sourceName = 'meets';

	private ?LeadSource $source = null;

	public function actionMigration(): void {
		IssueMeet::deleteAll();
		Yii::$app->leadManager->pushLead($this->createLead(IssueMeet::find()->one()));
		return;
		foreach (IssueMeet::find()->batch() as $rows) {
			foreach ($rows as $model) {
				/** @var IssueMeet $model */
				Yii::$app->leadManager->pushLead($this->createLead($model));
			}
		}
	}

	private function createLead(IssueMeet $meet): LeadForm {
		$lead = new LeadForm();
		$lead->phone = $meet->phone;
		$lead->email = $meet->email;
		$lead->postal_code = $meet->customerAddress->postal_code ?? null;
		$lead->datetime = $meet->date_at;
		$lead->owner_id = $meet->agent_id;
		$lead->type_id = $this->getType($meet->type->name)->id;
		$lead->status_id = $this->getStatus($meet->getStatusName())->id;
		$lead->campaign_id = $this->getCampaign($meet->campaign->name)->id;
		$lead->source_id = $this->getSource($meet)->id;
		return $lead;
	}

	private function getSource(IssueMeet $issueMeet): LeadSource {
		if ($this->source === null) {
			$model = LeadSource::find()->andWhere(['name' => $this->sourceName])->one();
			if ($model === null) {
				$model = new LeadSource(['name' => $this->sourceName]);
				$model->save();
			}
			$this->source = $model;
		}
		return $this->source;
	}

	private function getCampaign(string $name): LeadCampaign {
		$models = LeadCampaign::getModels();
		foreach ($models as $model) {
			if ($model->name === $name) {
				return $model;
			}
		}
		$model = new LeadCampaign(['name' => $name]);
		$model->save();
		LeadCampaign::getModels(true);
		return $model;
	}

	private function getType(string $name): LeadType {
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
