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
	public bool $withOfficeCost = false;

	public $officeCost = [
		'value' => 30,
		'vat' => 0,
		'type' => IssueCost::TYPE_OFFICE,
	];

	public array $hiddenFields = [
		'type',
		'providerType',
		'payment_at',
		'costs_ids',
		'vat',
	];

	protected function saveCosts(): void {
		$cost = $this->createOfficeCost();
		if ($cost) {
			if (empty($cost->date_at)) {
				$cost->date_at = $this->getModel()->created_at;
			}
			$cost->save();
			$this->costs_ids[] = $cost->id;
		}
		parent::saveCosts();
	}

	public function isVisibleField(string $attribute): bool {
		return !in_array($attribute, $this->hiddenFields);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	private function createOfficeCost(): ?IssueCost {
		if (!$this->withOfficeCost) {
			return null;
		}
		if (!empty($this->officeCost)) {
			$options = $this->officeCost;
			if (!isset($options['class'])) {
				$options['class'] = IssueCost::class;
			}
			$options['issue_id'] = $this->getIssue()->id;
			return Yii::createObject($options);
		}
		return null;
	}
}
