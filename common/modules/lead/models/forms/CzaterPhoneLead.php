<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadSourceInterface;
use DateTime;
use yii\base\Model;

class CzaterPhoneLead extends Model implements LeadInterface {

	protected const CAMPAIGN_NAME = 'czater';

	public string $idDataset;
	public string $clientDirectional;
	public string $clientNumber;
	public string $dateRequested;
	public string $dateStart;
	public string $dateFinish;
	public string $duration;
	public string $addition_params;
	public string $status;

	public function getStatusId(): int {

		// TODO: Implement getStatusId() method.
	}

	public function getSourceId(): int {
		// TODO: Implement getSourceId() method.
	}


	public function getDateTime(): DateTime {
		return new DateTime($this->dateStart);
	}

	public function getData(): array {
		return $this->attributes;
	}

	public function getPhone(): string {
		return $this->clientDirectional . $this->clientNumber;
	}

	public function getEmail(): ?string {
		return null;
	}

	public function getPostalCode(): ?string {
		return null;
	}

	public function getOwnerId(): ?int {
		return $this->getSource()->getOwnerId();
	}

	public function getSource(): LeadSourceInterface {

	}

	public function getCampaignId(): ?int {
		$model = LeadCampaign::find()->andWhere(['name' => static::CAMPAIGN_NAME])->one();
		if ($model === null) {
			$model = new LeadCampaign(['name' => static::CAMPAIGN_NAME]);
			$model->save();
		}
		return $model->id;
	}
}
