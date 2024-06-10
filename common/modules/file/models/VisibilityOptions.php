<?php

namespace common\modules\file\models;

use common\models\user\User;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

/**
 * @see FileType
 */
class VisibilityOptions extends Model {

	public array $allowedRoles = [];
	public array $disallowedRoles = [];

	public array $usersIds = [];

	public static function getRolesNames(): array {
		return User::getRolesNames();
	}

	public function rules(): array {
		return [
			[['allowedRoles', 'disallowedRoles', 'usersIds'], 'default', 'value' => []],
			[
				['allowedRoles', 'disallowedRoles'], 'in',
				'range' => array_keys(static::getRolesNames()),
				'allowArray' => true,
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'allowedRoles' => Yii::t('file', 'Allowed Roles'),
			'disallowedRoles' => Yii::t('file', 'Disallowed Roles'),
			'usersIds' => Yii::t('file', 'Users'),
		];
	}

	public function toJson(): string {
		$values = $this->toArray();
		return Json::encode($values);
	}

	public static function createFromJson(string $json): self {
		$values = Json::decode($json);
		return new static($values);
	}

	protected function arrayAttributes(): array {
		return [
			'allowedRoles',
			'disallowedRoles',
		];
	}

	public function setAttributes($values, $safeOnly = true) {
		$this->ensureArrayAttributes($values);
		parent::setAttributes($values, $safeOnly);
	}

	private function ensureArrayAttributes(array &$values) {
		foreach ($values as $name => $value) {
			if (in_array($name, $this->arrayAttributes()) && empty($value)) {
				$values[$name] = [];
			}
		}
	}
}
