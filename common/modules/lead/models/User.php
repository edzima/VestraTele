<?php

namespace common\modules\lead\models;

use common\models\user\UserProfile;
use yii\db\ActiveQuery;

/**
 * @property-read int $id
 * @property-read string $email
 * @property-read string $username
 */
class User extends ActiveRecord implements LeadUserInterface {

	public static function tableName(): string {
		return '{{%user}}';
	}

	public function __toString(): string {
		return $this->getFullName();
	}

	public function getID(): int {
		return $this->id;
	}

	public function getUserProfile(): ActiveQuery {
		return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
	}

	public function getFullName(): string {
		return $this->username;
	}

	public function getEmail(): string {
		return $this->email;
	}
}
