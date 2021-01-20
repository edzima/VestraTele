<?php

namespace backend\modules\settlement\models;

use common\models\forms\HiddenFieldsModel;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use Yii;

class AdministrativeCalculationForm extends CalculationForm implements HiddenFieldsModel {

	public $type = IssuePayCalculation::TYPE_ADMINISTRATIVE;
	public string $value = '130';
	public ?string $vat = '23';
	public ?int $providerType = IssuePayCalculation::PROVIDER_CLIENT;

	public $officeCost = [
		'value' => 130,
		'vat' => 23,
		'type' => IssueCost::TYPE_OFFICE,
	];

	public array $hiddenFields = [
		'type',
		'providerType',
		'payment_at',
		'costs_ids',
	];

	protected function saveCosts(): void {
		$cost = $this->createOfficeCost();
		if ($cost) {
			$cost->save();
			$this->costs_ids[] = $cost->id;
		}
		parent::saveCosts();
	}

	public function isVisibleField(string $attribute): bool {
		return !in_array($attribute, $this->hiddenFields);
	}

	private function createOfficeCost(): ?IssueCost {
		if (!empty($this->officeCost)) {
			$options = $this->officeCost;
			if (!isset($options['class'])) {
				$options['class'] = IssueCost::class;
			}
			$options['issue_id'] = $this->issue_id;
			return Yii::createObject($options);
		}
		return null;
	}
}
