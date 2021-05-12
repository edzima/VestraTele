<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadQuestion;
use yii\base\Model;

class LeadReportsForm extends Model {

	public $status_id;
	public $selectedSchemas = [];
	private ActiveLead $lead;
	private int $owner_id;
	public $details;

	private ?array $models = null;

	public function rules(): array {
		return [
			['status_id', 'integer'],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
		];
	}

	public function __construct(int $owner_id, ActiveLead $lead, $config = []) {
		$this->owner_id = $owner_id;
		$this->lead = $lead;
		parent::__construct($config);
	}

	public function init() {
		if (empty($this->status_id)) {
			$this->status_id = $this->lead->getStatusId();
		}
		if (empty($this->selectedSchemas)) {

		}
		parent::init();
	}

	public function getTextModels(): array {
		return array_filter($this->getModels(), static function (LeadReportForm $form): bool {
			return $form->isTextField();
		});
	}

	public function getDropdownItems(): array {
		$items = [];
		foreach ($this->getModels() as $model) {
			if (!$model->isTextField()) {
				$items[$model->schema_id] = $model->getSchema()->name;
			}
		}
		return $items;
	}

	/**
	 * @return LeadReportForm[]
	 */
	public function getModels(): array {
		if ($this->models === null) {
			/** @var LeadReport[] $reports */
			$reports = $this->lead->getReports()
				->with('schema')
				->indexBy('schema_id')
				->all();
			$models = [];
			foreach ($reports as $report) {
				$models[$report->schema_id] = LeadReportForm::createFromModel($report);
			}
			foreach ($this->getSchemas() as $schema) {
				if (!isset($models[$schema->id])) {
					$model = new LeadReportForm($this->owner_id, $this->lead);
					$model->status_id = $this->status_id;
					$model->setSchema($schema);
					$models[$schema->id] = $model;
				}
			}
			$this->models = $models;
		}
		return $this->models;
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName)
			&& LeadReportForm::loadMultiple($this->getModels(), $data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors)
			&& LeadReportForm::validateMultiple($this->getModels());
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		foreach ($this->getTextModels() as $model) {
			$model->save(false);
		}
		return true;
	}

	public function getLead(): ActiveLead {
		return $this->lead;
	}

	public function getLeadTypeID(): int {
		return $this->lead->getSource()->getType()->getID();
	}

	/**
	 * @return LeadQuestion[]
	 */
	public function getSchemas(): array {
		return LeadQuestion::findWithStatusAndType($this->status_id, $this->getLeadTypeID());
	}

	public static function getStatusNames(): array {
		return LeadReportForm::getStatusNames();
	}

}
