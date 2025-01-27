<?php

namespace common\modules\court\modules\spi\entity\court;

class CourtDepartmentFullDTO extends CourtDepartmentSmallDTO {

	public ?string $email;
	public ?string $eternalId;
	public ?bool $applicatingBlocked;
	public bool $published;

	protected Court $court;

	public int $departmentNumberNumeral;

	public function setCourt($value): void {
		if ($value instanceof Court) {
			$this->court = $value;
		} else {
			if (is_array($value)) {
				$this->court = new Court($value);
			}
		}
	}

	public function getCourt(): Court {
		return $this->court;
	}
}
