<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;
use common\models\User;

/**
 * Create user form.
 */
class UserForm extends Model {

	public $username;
	public $email;
	public $password;
	public $status;
	public $roles;
	public $parent_id;

	private $model;

	public function getParents(): array {
		$list = User::getSelectList([User::ROLE_AGENT]);
		if (!$this->getModel()->isNewRecord) {
			if (isset($list[$this->getModel()->id])) {
				unset($list[$this->getModel()->id]);
			}
		}
		return $list;
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['username', 'trim'],
			['username', 'required'],
			['username', 'match', 'pattern' => '#^[\w_\-\.]+$$#i'],
			[
				'username', 'unique',
				'targetClass' => User::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
			['username', 'string', 'min' => 2, 'max' => 32],

			['email', 'trim'],
			['email', 'required'],
			['email', 'email'],
			[
				'email', 'unique',
				'targetClass' => User::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},

			],
			['email', 'string', 'max' => 255],

			['password', 'required', 'on' => 'create'],
			['password', 'string', 'min' => 6, 'max' => 32],
			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['parent_id' => 'id']],
			['parent_id', 'in', 'range' => array_keys($this->getParents())],
			['status', 'integer'],
			['status', 'in', 'range' => array_keys(User::statuses())],
			[
				'roles', 'each',
				'rule' => [
					'in', 'range' => ArrayHelper::getColumn(
						Yii::$app->authManager->getRoles(),
						'name'
					),
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'username' => Yii::t('backend', 'Login'),
			'email' => Yii::t('backend', 'Email'),
			'password' => Yii::t('backend', 'Hasło'),
			'status' => Yii::t('backend', 'Status'),
			'roles' => Yii::t('backend', 'Rola'),
			'parent_id' => 'Przełożony',
		];
	}

	/**
	 * @inheritdoc
	 */
	public function setModel(User $model) {
		$this->username = $model->username;
		$this->email = $model->email;
		$this->status = $model->status;
		$this->model = $model;
		$this->parent_id = $model->boss;
		$this->roles = ArrayHelper::getColumn(
			Yii::$app->authManager->getRolesByUser($model->getId()),
			'name'
		);

		return $this->model;
	}

	/**
	 * @inheritdoc
	 */
	public function getModel(): User {
		if (!$this->model) {
			$this->model = new User();
		}

		return $this->model;
	}

	public function save(): bool {
		if ($this->validate()) {
			$model = $this->getModel();
			$isNewRecord = $model->getIsNewRecord();
			$model->username = $this->username;
			$model->email = $this->email;
			$model->status = $this->status;
			$model->boss = $this->parent_id;
			if ($this->password) {
				$model->setPassword($this->password);
			}
			$model->generateAuthKey();
			if ($model->save()) {
				if ($isNewRecord) {
					$model->afterSignup();
				}
				$auth = Yii::$app->authManager;
				$auth->revokeAll($model->getId());

				if ($this->roles && is_array($this->roles)) {
					foreach ($this->roles as $role) {
						$auth->assign($auth->getRole($role), $model->getId());
					}
				}
			}
			if ($model->hasErrors()) {
				$this->addErrors($model->getErrors());
			}
			return !$model->hasErrors();
		}

		return false;
	}
}
