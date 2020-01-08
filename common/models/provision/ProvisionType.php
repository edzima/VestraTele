<?php

namespace common\models\provision;

use backend\modules\provision\models\ProvisionTypeForm;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "provision_type".
 *
 * @property int $id
 * @property string $name
 * @property string $date_from
 * @property string $date_to
 * @property boolean $only_with_tele
 * @property boolean is_default
 * @property string $data
 * @property string $value
 * @property boolean $is_percentage
 *
 * @property-read ProvisionUser[] $provisionUsers
 */
class ProvisionType extends ActiveRecord {

	private const KEY_DATA_ROLES = 'roles';
	private const KEY_DATA_TYPES = 'types';

	public function __toString() {
		return $this->name . ' ' . $this->getFormattedValue();
	}

	public function beforeSave($insert): bool {
		$this->setDataArray($this->getDataArray());
		return parent::beforeSave($insert);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'provision_type';
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
			'date_from' => 'Od',
			'date_to' => 'Do',
			'only_with_tele' => 'Tylko z tele',
			'is_default' => 'Domyślna',
			'value' => 'Prowizja',
			'is_percentage' => 'Procentowa',
			'rolesNames' => 'Użytkownicy',
			'typesNames' => 'Typy spraw',
		];
	}



	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProvisionUsers() {
		return $this->hasMany(ProvisionUser::class, ['type_id' => 'id']);
	}

	public function setRoles(array $roles): void {
		$this->setDataValues(static::KEY_DATA_ROLES, $roles);
	}

	public function getRoles(): array {
		return $this->getDataArray()[static::KEY_DATA_ROLES] ?? [];
	}

	public function getTypesIds(): array {
		return $this->getDataArray()[static::KEY_DATA_TYPES] ?? [];
	}

	public function setTypesIds(array $ids): void {
		$this->setDataValues(static::KEY_DATA_TYPES, $ids);
	}

	private function setDataValues(string $key, $values): void {
		$data = $this->getDataArray();
		$data[$key] = $values;
		$this->setDataArray($data);
	}

	private function getDataArray(): array {
		return Json::decode($this->data) ?? [];
	}

	private function setDataArray(array $data): void {
		$this->data = Json::encode($data);
	}

	public function isValidForUser(int $userId): bool {
		if (empty($this->getRoles())) {
			return true;
		}
		foreach ($this->getRoles() as $role) {
			if (in_array($userId, Yii::$app->authManager->getUserIdsByRole($role))) {
				return true;
			}
		}
		return false;
	}

	public function getFormattedValue($value = null): string {
		if ($value === null) {
			$value = $this->value;
		}
		if ($this->is_percentage) {
			return Yii::$app->formatter->asPercent($value / 100);
		}
		return Yii::$app->formatter->asCurrency($value);
	}

	public function getTypesNames(): string {
		$types = $this->getTypesIds();
		if (empty($types)) {
			return 'Wszystkie';
		}
		$typesNames = ProvisionTypeForm::getTypesNames();
		$names = [];
		foreach ($types as $id) {
			$names[] = $typesNames[$id];
		}
		return implode(' ,', $names);
	}

	public function getRolesNames(): string {
		$roles = $this->getRoles();
		if (empty($roles)) {
			return 'Wszyscy';
		}
		$rolesNames = ProvisionTypeForm::getRolesNames();
		$names = [];
		foreach ($roles as $role) {
			$names[] = $rolesNames[$role];
		}
		return implode(' ,', $names);
	}

	/**
	 * {@inheritdoc}
	 * @return ProvisionTypeQuery the active query used by this AR class.
	 */
	public static function find() {
		return new ProvisionTypeQuery(static::class);
	}
}
