<?php

namespace common\modules\lead\components;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadInterface;
use Yii;
use yii\base\Component;

class LeadManager extends Component {

	public string $logCategory = 'lead';

	/**
	 * @var string|ActiveLead
	 */
	public string $model = Lead::class;

	public function findModel(int $id): ?ActiveLead {
		return $this->model::findById($id);
	}

	public function pushLead(LeadInterface $lead): bool {
		if (empty($lead->getPhone()) && empty($lead->getEmail())) {
			Yii::warning([
				'message' => 'Push lead without phone or email.',
				'lead' => $lead,
			], $this->logCategory);
			return false;
		}
		$model = $this->getModel($lead);
		return $model->save();
	}

	public function getModel(LeadInterface $lead): ActiveLead {
		$model = $this->find($lead);
		if ($model === null) {
			$model = $this->create($lead);
		}

		return $model;
	}

	private function find(LeadInterface $lead): ?ActiveLead {
		return $this->model::findByLead($lead);
	}

	private function create(LeadInterface $lead): ActiveLead {
		return $this->model::createFromLead($lead);
	}

}
