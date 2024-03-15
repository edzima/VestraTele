<?php

namespace common\modules\lead\models\forms;

use yii\base\Model;

class LeadMultipleUpdate extends Model {

	public $ids;

	private ?LeadSourceChangeForm $sourceModel = null;
	private ?LeadStatusChangeForm $statusModel = null;
	private ?LeadsUserForm $usersModel = null;

	public static function getSourcesNames(): array {
		return LeadSourceChangeForm::getSourcesNames();
	}

	public function load($data, $formName = null) {
		$this->typeToInt($data, $this->getSourceModel()->formName(), 'source_id');
		$this->typeToInt($data, $this->getStatusModel()->formName(), 'status_id');

		return $this->getSourceModel()->load($data, $formName)
			&& $this->getStatusModel()->load($data, $formName)
			&& $this->getUsersModel()->load($data, $formName);
	}

	protected function typeToInt(array &$data, string $formName, string $attribute): void {
		if (isset($data[$formName][$attribute])) {
			$data[$formName][$attribute] = empty($data[$formName][$attribute])
				? null
				: (int) $data[$formName][$attribute];
		}
	}

	public function rules(): array {
		return [
			['ids', 'required'],
		];
	}

	public function updateSource(): ?int {
		$model = $this->getSourceModel();
		if (empty($model->source_id)) {
			return null;
		}
		$model->ids = $this->ids;
		return $model->save();
	}

	public function updateStatus(): ?int {
		$model = $this->getStatusModel();
		if (empty($model->status_id)) {
			return null;
		}
		$model->ids = $this->ids;
		return $model->save();
	}

	public function updateUsers(): ?int {
		$model = $this->getUsersModel();
		if (empty($model->userId)) {
			return null;
		}
		$model->leadsIds = $this->ids;
		return $model->save();
	}

	public function getSourceModel(): LeadSourceChangeForm {
		if ($this->sourceModel === null) {
			$this->sourceModel = new LeadSourceChangeForm([
				'scenario' => LeadSourceChangeForm::SCENARIO_NOT_REQUIRED,
			]);
		}
		return $this->sourceModel;
	}

	public function getStatusModel(): LeadStatusChangeForm {
		if ($this->statusModel === null) {
			$this->statusModel = new LeadStatusChangeForm([
					'scenario' => LeadStatusChangeForm::SCENARIO_NOT_REQUIRED,
				]
			);
		}
		return $this->statusModel;
	}

	public function getUsersModel(): LeadsUserForm {
		if ($this->usersModel === null) {
			$this->usersModel = new LeadsUserForm(
				[
					'scenario' => LeadsUserForm::SCENARIO_NOT_REQUIRED,
				]
			);
		}
		return $this->usersModel;
	}
}
