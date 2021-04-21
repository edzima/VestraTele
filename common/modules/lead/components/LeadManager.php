<?php

namespace common\modules\lead\components;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadInterface;
use Yii;
use yii\base\Component;

class LeadManager extends Component {

	/**
	 * @var string|Lead
	 */
	public string $model = Lead::class;

	public function findById(string $id): ?ActiveLead {
		return $this->model::findById($id);
	}

	public function pushLead(LeadInterface $lead): ?ActiveLead {
		if (empty($lead->getPhone()) && empty($lead->getEmail())) {
			Yii::warning([
				'message' => 'Push lead without phone or email.',
				'lead' => $lead,
			], 'lead.push.empty-contact');
			return null;
		}

		$model = $this->create($lead);
		if ($model->validate()) {
			Yii::info([
				'message' => 'Push new lead.',
				'lead' => $lead,
			], 'lead.push.create.success');
			$model->save();
		} else {
			Yii::warning([
				'message' => 'Try push lead with validate errors.',
				'lead' => $lead,
				'errors' => $model->getErrors(),
			], 'lead.push.create.error');
		}
		return $model;
	}

	public function groupLeads(LeadInterface $lead): void {
		$models = $this->findByLead($lead);
		if (count($models) > 1) {
			foreach ($models as $model) {
				$model->updateFromLead($lead);
			}
		}
	}

	/**
	 * @param LeadInterface $lead
	 * @return Lead
	 */
	protected function create(LeadInterface $lead): ActiveLead {
		/** @var ActiveLead $model */
		$model = Yii::createObject($this->model);
		$model->setLead($lead);
		return $model;
	}

	/**
	 * @param LeadInterface $lead
	 * @return Lead[]
	 */
	public function findByLead(LeadInterface $lead): array {
		return $this->model::findByLead($lead);
	}

}
