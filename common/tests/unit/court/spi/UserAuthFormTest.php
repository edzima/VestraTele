<?php

namespace common\tests\unit\court\spi;

use common\fixtures\helpers\UserFixtureHelper;
use common\modules\court\modules\spi\models\auth\SpiUserAuth;
use common\modules\court\modules\spi\models\auth\SpiUserAuthForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;
use yii\test\ActiveFixture;

class UserAuthFormTest extends Unit {

	use UnitModelTrait;

	private SpiUserAuthForm $model;

	public function _fixtures(): array {
		return [
			'agent' => UserFixtureHelper::agent(),
			'userAuth' => [
				'class' => ActiveFixture::class,
				'modelClass' => SpiUserAuth::class,
				'data' => [],
			],
		];
	}

	public function testEmpty() {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Username cannot be blank.', 'username');
		$this->thenSeeError('User ID cannot be blank.', 'user_id');
		$this->model->scenario = SpiUserAuthForm::SCENARIO_CREATE;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Password cannot be blank.', 'password');
	}

	public function getModel(): Model {
		return $this->model;
	}

	private function giveModel(string $encryptionKey = 'testSecretKey', array $config = []): void {
		$this->model = new SpiUserAuthForm($encryptionKey, $config);
	}
}
