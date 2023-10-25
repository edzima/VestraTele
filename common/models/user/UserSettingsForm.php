<?php

namespace common\models\user;

use yii\base\Model;

class UserSettingsForm extends Model {

	public ?int $favoriteIssueTypeId = null;

	public int $userId;

	public function rules(): array {
		return [
			['favoriteIssueTypeId', 'integer', 'min' => 1],
			['favoriteIssueTypeId', 'default', 'value' => null],
		];
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		return UserProfile::updateAll([
				'favorite_issue_type_id' => $this->favoriteIssueTypeId,
			], [
					'user_id' => $this->userId,
				]
			) > 0;
	}
}
