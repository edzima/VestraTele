<?php

namespace backend\modules\provision\models;

use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\user\User;
use common\models\user\Worker;
use Yii;
use yii\base\Model;

class ProvisionUserForm extends Model {

	public const SCENARIO_SELF = 'self';

	public $from_user_id;
	public $to_user_id;
	public $value;
	public $from_at;
	public $to_at;
	public $type_id;

	private ?ProvisionUser $model = null;

	public static function createUserSelfForm(int $userId): self {
		$model = new static();
		$model->scenario = static::SCENARIO_SELF;
		$model->from_user_id = $userId;
		$model->to_user_id = $userId;
		return $model;
	}

	public function isSelf(): bool {
		return $this->scenario === static::SCENARIO_SELF ||
			(!$this->getModel()->isNewRecord && $this->getModel()->isSelf());
	}

	public function attributeLabels() {
		return $this->getModel()->attributeLabels();
	}

	public function rules(): array {
		return [
			[['value', 'from_user_id', 'to_user_id', 'type_id'], 'required'],
			[['type_id', 'from_user_id', 'to_user_id'], 'integer'],
			['type_id', 'in', 'range' => array_keys(static::getTypesNames())],
			[
				'value', 'number',
			],
			[
				'value', 'number', 'max' => 100, 'enableClientValidation' => false,
				'when' => function () {
					$type = $this->getType();
					return $type->is_percentage ?? false;
				},
			],
			['from_user_id', 'compare', 'compareAttribute' => 'to_user_id', 'on' => static::SCENARIO_SELF],
			[['from_at', 'to_at'], 'date', 'format' => 'php:Y-m-d H:i',],
			[
				'to_at', 'compare', 'compareAttribute' => 'from_at', 'operator' => '>=',
				'enableClientValidation' => false,
			],
			[
				'from_at', 'required', 'enableClientValidation' => false, 'when' => function (): bool {
				$exist = ProvisionUser::find()
					->andWhere([
						'from_user_id' => $this->from_user_id,
						'to_user_id' => $this->to_user_id,
						'type_id' => $this->type_id,
					]);
				if (!$this->getModel()->isNewRecord) {
					$exist->andWhere(['<>', 'id', $this->model->id]);
				}
				return $exist->exists();
			},
			],
			[
				'to_at', 'required', 'enableClientValidation' => false, 'when' => function (): bool {
				$exist = ProvisionUser::find()
					->andWhere([
						'from_user_id' => $this->from_user_id,
						'to_user_id' => $this->to_user_id,
						'type_id' => $this->type_id,
						'to_at' => null,
					])
					->andWhere(['IS NOT', 'from_at', null]);
				if (!$this->getModel()->isNewRecord) {
					$exist->andWhere(['<>', 'id', $this->model->id]);
				}
				return $exist->exists();
			},
			],
		];
	}

	public function setType(ProvisionType $type): void {
		$this->type_id = $type->id;
		$this->value = $type->value;
		$this->from_at = $type->from_at;
		$this->to_at = $type->to_at;
	}

	public function getType(): ?ProvisionType {
		if ($this->type_id) {
			return ProvisionType::getType($this->type_id, true);
		}
		return null;
	}

	public function setModel(ProvisionUser $model): void {
		$this->model = $model;
		$this->from_user_id = $model->from_user_id;
		$this->to_user_id = $model->to_user_id;
		if ($model->isSelf()) {
			$this->setScenario(static::SCENARIO_SELF);
		}
		$this->value = $model->value;
		$this->type_id = $model->type_id;
		$this->from_at = $model->from_at;
		$this->to_at = $model->to_at;
	}

	public function getModel(): ProvisionUser {
		if ($this->model === null) {
			$this->model = new ProvisionUser();
		}
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			Yii::warning($this->errors, __METHOD__);
			return false;
		}
		$model = $this->getModel();
		$model->value = $this->value;
		$model->from_user_id = $this->from_user_id;
		$model->to_user_id = $this->to_user_id;
		$model->type_id = $this->type_id;
		$model->from_at = $this->from_at;
		$model->to_at = $this->to_at;
		if ($model->save()) {
			return true;
		}
		Yii::warning($model->getErrors(), __METHOD__);
		return false;
	}

	public function getName(): string {
		if ($this->isSelf()) {
			return Yii::t('provision',
				'Self - {user}',
				['user' => $this->getModel()->toUser->getFullName()]
			);
		}
		return Yii::t('provision', '{fromUser} from {toUser}', [
			'fromUser' => $this->getModel()->fromUser->getFullName(),
			'toUser' => $this->getModel()->toUser->getFullName(),
		]);
	}

	public static function getTypesNames(): array {
		return ProvisionType::getTypesNames(true, true);
	}

	public static function getUserNames(): array {
		return User::getSelectList(User::getAssignmentIds(Worker::ROLES, false));
	}

}
