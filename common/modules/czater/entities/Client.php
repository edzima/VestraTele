<?php

namespace common\modules\czater\entities;

use yii\base\BaseObject;

class Client extends BaseObject {

	public string $id;
	public int $idClient;
	public string $name;
	public ?string $email;
	public ?string $phone;

	public ?string $skype;
	public ?string $custom;
	public ?string $google_id;
	public ?string $facebook_id;
	public string $description;
	public int $blocked;

	public string $firstReferer;
	public string $dateInitalize;
	public int $sessionCounter;
	public string $IP;
	public array $customFields;

}
