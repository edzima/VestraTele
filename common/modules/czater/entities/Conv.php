<?php

namespace common\modules\czater\entities;

use yii\base\BaseObject;

class Conv extends BaseObject {

	public int $id;
	public int $idOwner;
	public int $idConsultant;
	public ?string $consultantEmail;
	public int $idClient;
	public ?string $clientEmail;
	public ?string $dateBegin;
	public string $firstMessage;
	public ?string $referer;

}
