<?php

namespace common\modules\czater\entities;

use yii\base\BaseObject;

class Consultant extends BaseObject {

	public int $idUser;
	public string $username;
	public string $email;
	public string $first_name;
	public string $last_name;
	public string $phone_directional;
	public string $phone_number;
	public string $status;
	public ?string $sections;
	public ?string $sections_ids;

	public function getSections(): array {
		return explode(',', $this->sections);
	}

	public function getSectionsIds(): array {
		return explode(',', $this->sections_ids);
	}
}
