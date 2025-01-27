<?php

namespace common\modules\court\modules\spi\entity\court;

use yii\base\Model;

class RepertoryDTO extends Model {

	public int $id;
	public string $departmentNumber;
	public string $name;
	public string $externalId;
	public bool $published;

	private CourtDepartmentFullDTO $courtDepartment;

	public function setCourtDepartment($value): void {
		if ($value instanceof CourtDepartmentFullDTO) {
			$this->courtDepartment = $value;
		} else {
			$department = new CourtDepartmentFullDTO($value);
			$this->courtDepartment = $department;
		}
	}

	public function getCourtDepartment(): CourtDepartmentFullDTO {
		return $this->courtDepartment;
	}
}
