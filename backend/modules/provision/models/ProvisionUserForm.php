<?php

namespace backend\modules\provision\models;

use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ProvisionUserForm extends Model {

	/**
	 * @var ProvisionType
	 */
	private $types;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var ProvisionUser[]
	 */
	private $models;

	public function __construct(User $user, $config = []) {
		$this->user = $user;
		parent::__construct($config);
	}

	public function getUser(): User {
		return $this->user;
	}

	/**
	 * @param ProvisionType[] $types
	 */
	public function setTypes(array $types): void {
		$this->types = $types;
	}

	public function getTypesNames(): array {
		return ArrayHelper::map($this->getTypes(), 'id', 'name');
	}

	public function getParentsModels(): array {
		if (!$this->user->hasParent()) {
			return [];
		}
		return array_filter($this->getModels(), function (ProvisionUser $provisionUser) {
			return $provisionUser->from_user_id === $this->user->id && $provisionUser->to_user_id !== $this->user->id;
		});
	}

	public function getChildesModels(): array {
		return array_filter($this->getModels(), function (ProvisionUser $provisionUser) {
			return $provisionUser->to_user_id === $this->user->id && $provisionUser->from_user_id !== $this->user->id;
		});
	}

	public function getSelfModels(): array {
		return array_filter($this->getModels(), function (ProvisionUser $provisionUser) {
			return $provisionUser->to_user_id === $this->user->id
				&& $provisionUser->from_user_id === $this->user->id;
		});
	}

	/**
	 * @return ProvisionType[]
	 */
	private function getTypes(): array {
		if (empty($this->types)) {
			$types = Yii::$app->provisions->getTypes();
			if (empty($types)) {
				throw new InvalidConfigException('Provision type must be set');
			}
			$this->types = $types;
		}
		return $this->types;
	}

	public function load($data, $formName = null): bool {
		return static::loadMultiple($this->getModels(), $data, $formName);
	}

	public function save(): bool {
		if ($this->validate()) {
			foreach ($this->getModels() as $model) {
				$model->save();
			}
			return true;
		}
		return false;
	}

	/**
	 * @return ProvisionUser[]
	 */
	private function getModels(): array {
		if ($this->models === null) {
			$this->models = $this->createModels($this->getExistedModels());
		}

		return $this->models;
	}

	/**
	 * @return ProvisionUser[]
	 */
	private function getExistedModels(): array {
		return ProvisionUser::find()
			->with('type')
			->user($this->user)
			->all();
	}

	/**
	 * @param ProvisionUser[] $existed
	 * @return ProvisionUser[]
	 */
	private function createModels(array $existed): array {
		$models = [];
		$models = array_merge($models, $this->createSelfModels($existed));

		$parentsExistedIds = ArrayHelper::map($existed, 'type_id', 'to_user_id', 'to_user_id');
		foreach ($this->user->getParentsIds() as $id) {
			$models = array_merge($models, $this->createTypesModels($this->user->id, $id, (array) ($parentsExistedIds[$id] ?? [])));
		}
		$childesExistedIds = ArrayHelper::map($existed, 'type_id', 'from_user_id', 'from_user_id');
		foreach ($this->user->getAllChildesIds() as $id) {
			$models = array_merge($models, $this->createTypesModels($id, $this->user->id, (array) ($childesExistedIds[$id] ?? [])));
		}

		return array_merge($models, $existed);
	}

	/**
	 * @param ProvisionUser[] $existed
	 * @return ProvisionUser[]
	 */
	private function createSelfModels(array $existed): array {
		$selfExisted = [];
		foreach ($existed as $provisionUser) {
			if ($provisionUser->from_user_id === $this->user->id && $provisionUser->to_user_id === $this->user->id) {
				$selfExisted[$provisionUser->type_id] = $this->user->id;
			}
		}
		return $this->createTypesModels($this->user->id, $this->user->id, $selfExisted);
	}

	/**
	 * @param int $fromUserId
	 * @param int $toUserId
	 * @param array $excluded
	 * @return ProvisionUser[]
	 */
	private function createTypesModels(int $fromUserId, int $toUserId, array $excluded = []): array {
		$models = [];
		foreach ($this->getTypes() as $type) {
			if (!isset($excluded[$type->id])) {
				$model = $this->createModel($fromUserId, $toUserId, $type);
				if ($model !== null) {
					$models[] = $model;
				}
			}
		}
		return $models;
	}

	private function createModel(int $fromUser, int $toUser, ProvisionType $type): ?ProvisionUser {
		if (!Yii::$app->provisions->isTypeForUser($type, $fromUser)) {
			return null;
		}
		return new ProvisionUser([
			'from_user_id' => $fromUser,
			'to_user_id' => $toUser,
			'type' => $type,
			'value' => $type->value,
		]);
	}

}
