<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\Module;
use yii\base\Model;

class LeadMarketMultipleForm extends Model {

	public array $leadsIds = [];
	public $creator_id;
	public $status;
	public string $details = '';

	private ?LeadMarketOptions $options = null;

	public static function getStatusesNames(): array {
		return LeadMarket::getStatusesNames();
	}

	public function rules(): array {
		return [
			[['leadsIds', 'status', '!creator_id'], 'required'],
			['creator_id', 'integer'],
			['leadsIds', 'each', 'rule' => ['integer']],
			['details', 'string'],
			[['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['creator_id' => 'id']],
			[['leadsIds'], 'exist', 'allowArray' => true, 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => 'id'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	public function load($data, $formName = null): bool {
		return parent::load($data, $formName)
			&& $this->getOptions()->load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
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
				'creator_id' => $this->creator_id,
				'lead_id' => $leadId,
				'status' => $this->status,
				'details' => $this->details,
				'options' => $this->getOptions()->toJson(),
			];
		}
		return LeadMarket::getDb()->createCommand()
			->batchInsert(LeadMarket::tableName(), [
				'creator_id',
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
