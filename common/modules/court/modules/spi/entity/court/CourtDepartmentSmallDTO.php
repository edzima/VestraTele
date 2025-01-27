<?php

namespace common\modules\court\modules\spi\entity\court;

use yii\base\Model;

class CourtDepartmentSmallDTO extends Model {

	public int $id;
	public string $departmentNumber;
	public string $name;
	public string $identifier;

}
