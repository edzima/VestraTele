<?php

namespace frontend\models;

use yii\base\Model;

class BenefitAmountAlignmentForm extends Model {

	public const MAX_MONTH = -36;

	public $lawsuitAt;
	public $benefitFromAt;
	public $benefitToAt;

	public function rules(): array {
		return [
			[['benefitToAt', 'lawsuitAt'], 'required'],
			[['lawsuitAt', 'benefitFromAt', 'benefitToAt'], 'date', 'format' => DATE_ATOM],
			['benefitFromAt', 'compare', 'compareAttribute' => 'benefitToAt', 'operator' => '<', 'enableClientValidation' => false],
			['benefitToAt', 'compare', 'compareAttribute' => 'lawsuitAt', 'operator' => '<', 'enableClientValidation' => false],
			[['benefitToAt', 'benefitFromAt'], 'compare', 'compareValue' => date(DATE_ATOM), 'operator' => '<=', 'message' => 'Data musi być z przeszlości'],
		];
	}

	public function attributeLabels() {
		return [
			'lawsuitAt' => 'Złożenie pozwu',
			'benefitFromAt' => 'Początek pobierania SZO/ZDO',
			'benefitToAt' => 'Data przyznania ŚP',
		];
	}

	public function calculateFrom(): int {
		if ($this->benefitFromAt === null || $this->benefitFromAtIsOlder()) {
			$this->lawsuitAt = $this->firstDayOfMonth($this->lawsuitAt);
			return strtotime(static::getMonthInterval(static::MAX_MONTH + 1), strtotime($this->lawsuitAt));
		}
		$this->benefitFromAt = $this->firstDayOfMonth($this->benefitFromAt);
		return strtotime($this->benefitFromAt);
	}

	private function benefitFromAtIsOlder(): bool {
		return strtotime($this->benefitFromAt) < strtotime(static::getMonthInterval());
	}

	public function calculateTo(): int {
		$this->benefitToAt = $this->firstDayOfMonth($this->benefitToAt);
		return strtotime('-1 month', strtotime($this->benefitToAt));
	}

	private function firstDayOfMonth(string $date): string {
		return date('Y-m-01', strtotime($date));
	}

	private static function getMonthInterval(int $months = self::MAX_MONTH): string {
		return "$months months";
	}

}