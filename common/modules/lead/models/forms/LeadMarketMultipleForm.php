<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadMarket;
use yii\base\Model;

class LeadMarketMultipleForm extends Model {

	public array $leadsIds = [];
	public $status;
	public string $details = '';

	private ?LeadMarketOptions $options = null;

	public static function getStatusesNames(): array {
		return LeadMarket::getStatusesNames();
	}

	public function rules(): array {
		return [
			[['leadsIds', 'status'], 'required'],
			['leadsIds', 'each', 'rule' => ['integer']],
			['details', 'string'],
			[['leadsIds'], 'exist', 'allowArray' => true, 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => 'id'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getOptions()->validate($attributeNames, $clearErrors);
	}

	public function save(): ?int {
		if (!$this->validate()) {
			return null;
		}
		$rows = [];
		foreach ($this->leadsIds as $leadId) {
			$rows[$leadId] = [
				'lead_id' => $leadId,
				'status' => $this->status,
				'details' => $this->details,
				'options' => $this->getOptions()->toString(),
			];
		}
		return LeadMarket::getDb()->createCommand()
			->batchInsert(LeadMarket::tableName(), [
				'lead_id',
				'status',
				'details',
				'options',
			], $rows)->execute();
	}

	public function getOptions(): LeadMarketOptions {
		if ($this->options === null) {
			$this->options = new LeadMarketOptions();
		}
		return $this->options;
	}

	public function setOptions(LeadMarketOptions $options): void {
		$this->options = $options;
	}

}
