<?php

namespace common\modules\court\modules\spi\models\auth;

use yii\base\Model;

class SpiUserAuthForm extends Model {

	public const SCENARIO_CREATE = 'create';

	public $username;
	public $password;

	public ?int $user_id = null;
	private ?SpiUserAuth $model = null;
	private string $encryptKey;

	public function __construct(string $encryptKey, array $config = []) {
		parent::__construct($config);
		$this->encryptKey = $encryptKey;
	}

	public function rules(): array {
		return [
			['!user_id', 'required'],
			[['username'], 'required'],
			['password', 'required', 'on' => static::SCENARIO_CREATE],
			[['username', 'password'], 'string'],
			[['username', 'password'], 'trim'],
		];
	}

	public function attributeLabels(): array {
		return SpiUserAuth::instance()->attributeLabels();
	}

	public function getModel(): SpiUserAuth {
		if ($this->model === null) {
			$model = $this->findUserModel();
			if ($model === null) {
				$model = new SpiUserAuth();
				$model->user_id = $this->user_id;
			}
			$this->model = $model;
		}
		return $this->model;
	}

	public function setModel(SpiUserAuth $model): void {
		$this->model = $model;
		$this->user_id = $model->user_id;
		$this->username = $model->username;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->username = $this->username;
		if (!empty($this->password)) {
			$model->encryptPassword($this->password, $this->encryptKey);
		}
		return $model->save();
	}

	public function findUserModel(): ?SpiUserAuth {
		if ($this->user_id) {
			return SpiUserAuth::findByUserId($this->user_id);
		}
		return null;
	}
}
