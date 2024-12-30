<?php

namespace common\modules\court\modules\spi\models\lawsuit;

use yii\base\BaseObject;

class LawsuitPartyDTO extends BaseObject {

	public int $id;
	public ?string $role;
	public string $name;
	public ?string $address;
	public ?string $type;
	public ?int $priority;
	public ?int $parentId;
	public ?string $status;
	public ?string $dateFrom;
	public ?string $dateTo;

	public ?string $gainedAccessDate;
	public ?array $representatives;
	public string $createdDate;
	public string $modificationDate;
}
