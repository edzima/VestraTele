<?php

namespace common\modules\court\modules\spi\components;

class LawsuitSignature {

	public const DEFAULT_PATTERN = '/^([A-Za-z]+) ([A-Za-z]+) (\d+)\/(\d+)$/';

	private string $signature;

	private string $departmentName;
	private string $repertoryName;
	private int $number;
	private int $year;
	private string $pattern;

	public function __construct(string $signature, string $pattern = self::DEFAULT_PATTERN) {
		$this->signature = $signature;
		$this->pattern = $pattern;
	}

	public function explode(): bool {
		if (preg_match($this->pattern, $this->signature, $matches)) {
			if (count($matches) === 5) {
				$this->departmentName = $matches[1];
				$this->repertoryName = $matches[2];
				$this->number = $matches[3];
				$this->year = $matches[4];
				return true;
			}
		}
		return false;
	}

	protected function setSignature(string $signature): void {
		$this->signature = $signature;
	}

	public function getDepartmentName(): string {
		return $this->departmentName;
	}

	public function getRepertoryName(): string {
		return $this->repertoryName;
	}

	public function getNumber(): int {
		return $this->number;
	}

	public function getYear(): int {
		return $this->year;
	}

}
