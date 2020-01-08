<?php

namespace backend\modules\provision\models;

use common\models\issue\IssueType;
use common\models\provision\ProvisionType;
use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ProvisionTypeForm extends Model {

	public $name;
	public $only_with_tele;
	public $is_default;
	public $rolesIds;
	public $typesIds;
	public $date_from;
	public $date_to;
	public $value;
	public $is_percentage = true;

	private $model;

	public function rules(): array {
		return [
			[['name', 'value'], 'required'],
			['name', 'string', 'max' => 255],
			['value', 'number', 'min' => 0, 'max' => 100],
			[['only_with_tele', 'is_default', 'is_percentage'], 'boolean'],
			['typesIds', 'in', 'range' => array_keys(static::getTypesNames()), 'allowArray' => true],
			['rolesIds', 'in', 'range' => array_keys(static::getRolesNames()), 'allowArray' => true],
			[['date_to', 'date_from'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return array_merge($this->getModel()->attributeLabels(), [
			'rolesIds' => 'Typ pracownik',
			'typesIds' => 'Typy spraw',
			'value' => 'Prowizja',
		]);
	}

	public function setModel(ProvisionType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->only_with_tele = $model->only_with_tele;
		$this->is_default = $model->is_default;
		$this->date_from = $model->date_from;
		$this->date_to = $model->date_to;
		$this->rolesIds = $model->getRoles();
		$this->typesIds = $model->getTypesIds();
		$this->is_percentage = $model->is_percentage;
		$this->value = $model->value;
	}

	public function getModel(): ProvisionType {
		if ($this->model === null) {
			$this->model = new ProvisionType();
		}
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->value = $this->value;
		$model->only_with_tele = $this->only_with_tele;
		$model->is_default = $this->is_default;
		$model->date_from = $this->date_from;
		$model->date_to = $this->date_to;
		$model->setTypesIds(is_array($this->typesIds) ? $this->typesIds : []);
		$model->setRoles(is_array($this->rolesIds) ? $this->rolesIds : []);
		$model->is_percentage = $this->is_percentage;
		return $model->save();
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(IssueType::find()
			->select('id,name')
			->all(), 'id', 'name');
	}

	public static function getRolesNames(): array {
		return [
			User::ROLE_AGENT => 'Agent',
			User::ROLE_TELEMARKETER => 'Tele',
			User::ROLE_LAYER => 'Prawnik',
		];
	}

}
