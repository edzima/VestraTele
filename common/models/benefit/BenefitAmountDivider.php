<?php

namespace common\models\benefit;

use yii\base\InvalidArgumentException;
use yii\base\Model;

class BenefitAmountDivider extends Model {

	private $month;
	private $smaller;
	private $greater;

	public function attributeLabels(): array {
		return [
			'month' => 'Miesiąc',
			'smaller' => BenefitAmount::getTypesNames()[BenefitAmount::TYPE_SMALLER],
			'greater' => BenefitAmount::getTypesNames()[BenefitAmount::TYPE_GREATER],
			'diff' => 'Różnica',
		];
	}

	public function getDiff(): float {
		return $this->greater - $this->smaller;
	}

	public function getSmaller(): float {
		return $this->smaller;
	}

	public function getGreater(): float {
		return $this->greater;
	}

	public function getMonth(): int {
		return $this->month;
	}

	public static function createForRanges(int $from, int $to): array {
		$models = [];
		 while ($from <= $to){
			$models[] = static::createForMonthTimestamp($from);
			$from = strtotime('+ 1 month', $from);
		}
		return $models;
	}

	public static function createForMonthTimestamp(int $timestamp): self {
		//	$timestamp = strtotime(date('Y-m-01', $timestamp));
		$divider = new static();
		foreach (BenefitAmount::instances() as $model) {
			if ($divider->smaller === null || $divider->greater === null) {
				if (strtotime($model->from_at) <= $timestamp && strtotime($model->to_at) >= $timestamp) {
					if ($model->isSmaller()) {
						$divider->smaller = $model->value;
					} else {
						$divider->greater = $model->value;
					}
				}
			} else {
				break;
			}
		}
		if ($divider->greater === null || $divider->smaller === null) {
			throw new InvalidArgumentException('Not found BenefitAmount data for month: ' . date('Y-m', $timestamp));
		}
		$divider->month = $timestamp;
		return $divider;
	}

}