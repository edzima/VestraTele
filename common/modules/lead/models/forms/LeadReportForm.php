<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadReportSchema;
use common\modules\lead\models\LeadReportSchemaStatusType;
use common\modules\lead\models\LeadStatus;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LeadReportForm extends Model {

	public $status_id;
	public $schema_id;
	public ?string $details = null;

	private ActiveLead $lead;
	private LeadReport $model;

	public function rules(): array {
		return [
			[['status_id', 'schema_id'], 'required'],
			[
				'details', 'required', 'when' => function () {
				$schema = LeadReportSchema::findOne($this->schema_id);
				return $schema && !empty($schema->placeholder);
			},
			],
		];
	}

	public function __construct(int $owner_id, ActiveLead $lead, $config = []) {
		$this->model = new LeadReport([
			'owner_id' => $owner_id,
			'lead_id' => $lead->getId(),
		]);
		$this->lead = $lead;
		$this->status_id = $lead->getStatusId();
		parent::__construct($config);
	}

	public static function createFromModel(LeadReport $leadReport): self {
		$model = new static($leadReport->owner_id, $leadReport->lead);
		$model->schema_id = $leadReport->schema_id;
		$model->details = $leadReport->details;
		$model->model = $leadReport;
		return $model;
	}

	public function getModel(): LeadReport {
		return $this->model;
	}

	public function getSchemaData(): array {
		$schemas = LeadReportSchemaStatusType::findSchemasByStatusAndType($this->status_id, $this->lead->getSource()->getType()->getID());
		return ArrayHelper::map(
			$schemas,
			'id',
			'name'
		);
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$lead = $this->lead;
		$statusId = (int) $this->status_id;
		$oldStatus = $lead->getStatusId();
		if ($oldStatus !== $statusId) {
			$lead->updateStatus($statusId);
		}
		$model = $this->model;
		$model->old_status_id = $oldStatus;
		$model->status_id = $statusId;
		$model->schema_id = $this->schema_id;
		$model->details = $this->details;
		return $model->save();
	}

	public function getLead(): LeadInterface {
		return $this->lead;
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

}
