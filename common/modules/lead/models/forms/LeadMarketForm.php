<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\LeadMarket;
use yii\base\Model;

class LeadMarketForm extends Model {

	public $lead_id;
	public $status;
	public string $details = '';

	private ?LeadMarket $model = null;
	private ?LeadMarketOptions $options = null;

	public static function getStatusesNames(): array {
		return LeadMarket::getStatusesNames();
	}

	public function rules(): array {
		return [
			[['lead_id', 'status'], 'required'],
			[['lead_id', 'status'], 'integer'],
			['details', 'string'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	public function load($attributeNames = null, $clearErrors = true): bool {
		return parent::load($attributeNames, $clearErrors)
			&& $this->getOptions()->load($attributeNames, $clearErrors);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getOptions()->validate($attributeNames, $clearErrors);
	}

	public function getModel(): LeadMarket {
		if ($this->model === null) {
			$this->model = new LeadMarket();
		}
		return $this->model;
	}

	public function setModel(LeadMarket $model): void {
		$this->model = $model;
		$this->lead_id = $model->lead_id;
		$this->status = $model->status;
		$this->details = $model->details;
		$this->setOptions($model->getMarketOptions());
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->lead_id = $this->lead_id;
		$model->status = $this->status;
		$model->options = $this->getOptions()->toJson();
		$model->details = $this->details;
		return $model->save();
	}

	public function getOptions(): LeadMarketOptions {
		if ($this->options === null) {
			$this->options = $this->getModel()->getMarketOptions();
		}
		return $this->options;
	}

	public function setOptions(LeadMarketOptions $options): void {
		$this->options = $options;
	}

}
