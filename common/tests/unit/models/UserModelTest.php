<?php

namespace common\tests\unit\models;

use common\fixtures\UserFixture;
use common\models\user\User;
use common\tests\unit\Unit;
use Yii;
use yii\rbac\ManagerInterface;

class UserModelTest extends Unit {

	protected ?User $model = null;

	private ?ManagerInterface $manger = null;

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures([
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
		]);
		$this->model = $this->tester->grabFixture('user', 0);
	}

	public function testAddEmptyRoles() {
		$this->model->setRoles([]);
		$this->checkAccess(User::ROLE_DEFAULT);
	}

	public function testAddFewRoles() {
		$roles = [User::ROLE_AGENT, User::ROLE_LAWYER];
		$this->model->setRoles($roles);
		foreach ($roles as $role) {
			$this->checkAccess($role);
		}
	}

	public function testAddNotExistRole() {
		$roleName = Yii::$app->security->generateRandomString();
		$this->model->setRoles([$roleName]);
		$this->checkAccess($roleName, false);
	}

	protected function checkAccess(string $role, bool $access = true) {
		$this->checkBoolCondition($this->getManager()->checkAccess($this->model->id, $role), $access);
		$this->checkBoolCondition(isset($this->model->getRoles()[$role]), $access);
	}

	protected function checkBoolCondition(bool $condition, bool $true): void {
		if (!$true) {
			$condition = !$condition;
		}
		$this->assertTrue($condition);
	}

	protected function getManager(): ManagerInterface {
		if ($this->manger === null) {
			$this->manger = Yii::$app->authManager;
		}
		return $this->manger;
	}

}
