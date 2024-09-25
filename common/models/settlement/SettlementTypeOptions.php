<?php

namespace common\models\settlement;

use common\models\issue\IssuePayCalculation;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

class SettlementTypeOptions extends Model {

	public const DEADLINE_1_WEEK = '+1 week';
	public const DEADLINE_2_WEEK = '+2 weeks';
	public const DEADLINE_1_MONTH = '+3 month';
	public const DEADLINE_LAST_DAY_OF_MONTH = 'last day of this month';
	public ?string $default_value = null;
	public ?string $vat = null;
	public ?int $provider_type = null;

	public ?string $deadline_range = null;

	public function rules(): array {
		return [
			[['default_value', 'vat'], 'number', 'min' => 0],
			['deadline_range', 'in', 'range' => array_keys($this->deadlineRangesNames())],
			['provider_type', 'in', 'range' => array_keys($this->providersTypesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'default_value' => Yii::t('settlement', 'Default value'),
			'vat' => Yii::t('settlement', 'VAT'),
			'provider_type' => Yii::t('settlement', 'Provider'),
			'providerTypeName' => Yii::t('settlement', 'Provider'),
			'deadline_range' => Yii::t('settlement', 'Deadline range'),
			'deadlineRangeName' => Yii::t('settlement', 'Deadline range'),
		];
	}

	public function getDefaultValue(): ?float {
		if ($this->default_value === null) {
			return null;
		}
		return (float) $this->default_value;
	}

	public function toJson(): string {
		$data = $this->toArray();
		$data = array_filter($data, function ($value): bool {
			return !empty($value);
		});
		ksort($data);
		return Json::encode($data);
	}

	public function getProviderTypeName(): ?string {
		return $this->providersTypesNames()[$this->provider_type] ?? null;
	}

	public function providersTypesNames(): array {
		return IssuePayCalculation::instance()->providersTypesNames();
	}

	public function getDeadlineRangeName(): ?string {
		return $this->deadlineRangesNames()[$this->deadline_range] ?? null;
	}

	public function deadlineRangesNames(): array {
		return [
			static::DEADLINE_1_WEEK => Yii::t('settlement', 'Deadline: 1 Week'),
			static::DEADLINE_2_WEEK => Yii::t('settlement', 'Deadline: 2 Week'),
			static::DEADLINE_1_MONTH => Yii::t('settlement', 'Deadline: 1 Month'),
			static::DEADLINE_LAST_DAY_OF_MONTH => Yii::t('settlement', 'Deadline: Last Day of Month'),
		];
	}

}
