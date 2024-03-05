<?php

namespace backend\modules\user\widgets;

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\models\UserForm;
use backend\modules\user\models\WorkerUserForm;
use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;
use common\widgets\CopyToClipboardBtn;
use yii\base\InvalidConfigException;

class CopyToCliboardFormAttributesBtn extends CopyToClipboardBtn {

	public ?User $model = null;
	public ?UserForm $formModel = null;

	protected function createUserForm(): UserForm {
		switch (get_class($this->model)) {
			case User::class:
				return new UserForm();
			case Worker::class:
				return new WorkerUserForm();
			case Customer::class:
				return new CustomerUserForm();
			default:
				throw new InvalidConfigException('Invalid $model class.');
		}
	}

	public function init(): void {
		if ($this->formModel === null) {
			if ($this->model === null) {
				throw new InvalidConfigException('$formModel or $model must be set.');
			}
			$this->formModel = $this->createUserForm();
			$this->formModel->setModel($this->model);
		}

		$this->copyText = $this->formModel->formToJson();

		parent::init();
	}
}
