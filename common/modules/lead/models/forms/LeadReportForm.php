<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadQuestion;
use common\modules\lead\models\LeadStatus;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LeadReportForm extends Model {

	public $status_id;
	public $schema_id;
	public ?string $details = null;

	private ActiveLead $lead;
	private LeadReport $model;
	/**
	 * @var LeadQuestion|mixed
	 */
	private ?LeadQuestion $schema = null;

	public function rules(): array {
		return [
			[['status_id', 'schema_id'], 'required'],
			[
				'details', 'required',
				'when' => function () {
					$schema = $this->getSchema();
					return $schema->is_required ?? false;
				},
				'enableClientValidation' => false,
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'details' => $this->getSchema()->name,
		];
	}

	public function getSchema(): ?LeadQuestion {
		if ($this->schema === null || $this->schema_id !== $this->schema->id) {
			$this->schema = LeadQuestion::findOne($this->schema_id);
		}
		return $this->schema;
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getSchemas(): array {
		return LeadQuestion::findWithStatusAndType($this->status_id, $this->getLeadTypeID());
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
		$model->setModel($leadReport);
		return $model;
	}

	private function setModel(LeadReport $model): void {
		$this->model = $model;
		$this->setSchema($model->schema);
		$this->details = $model->details;
	}

	public function setSchema(LeadQuestion $schema): void {
		$this->schema = $schema;
		$this->schema_id = $schema->id;
	}

	public function getModel(): LeadReport {
		return $this->model;
	}

	public function getLeadTypeID(): int {
		return $this->getLead()->getSource()->getType()->getID();
	}

	public function getSchemaData(): array {
		$schemas = LeadQuestion::findWithStatusAndType($this->status_id, $this->getLeadTypeID());

		return ArrayHelper::map(
			$schemas,
			'id',
			'name'
		);
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$lead = $this->lead;
		$statusId = (int) $this->status_id;
		$oldStatus = $lead->getStatusId();
		if ($oldStatus !== $statusId) {
			$lead->updateStatus($statusId);
		}
		if ($this->isTextField() && empty($this->details)) {
			if (!$this->model->isNewRecord) {
				$this->model->delete();
			}
			return true;
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

	public function isTextField(): bool {
		return !empty($this->getSchema()->placeholder);
	}

}
