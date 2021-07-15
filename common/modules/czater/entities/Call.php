<?php

namespace common\modules\czater\entities;

use yii\base\BaseObject;

class Call extends BaseObject {

	public int $id;
	public string $clientDirectional;
	public string $clientNumber;
	public ?string $clientName;
	public int $duration;
	public string $dateRequested;
	public string $status;

	public ?string $consultantName;
	public ?string $consultantNumber;
	public ?string $dateStart;
	public ?string $dateFinish;

}
