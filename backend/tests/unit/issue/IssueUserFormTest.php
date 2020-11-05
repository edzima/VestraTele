<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueUserForm;
use backend\tests\fixtures\IssueFixtureHelper;
use backend\tests\unit\Unit;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\Customer;
use Yii;

class IssueUserFormTest extends Unit {

	protected function _before() {
		parent::_before();
		$this->tester->haveFixtures(IssueFixtureHelper::fixtures());
	}

	public function testLink() {
		$model = new IssueUserForm();
		$model->setIssue($this->grabIssue(0));
		$model->user_id = 101;
		$model->type = IssueUser::TYPE_VICTIM;
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssueUser::class, [
			'issue_id' => $model->getIssue()->id,
			'user_id' => 101,
			'type' => IssueUser::TYPE_VICTIM,
		]);
	}

	public function testUserWithoutRole() {
		$model = new IssueUserForm();
		$model->setIssue($this->grabIssue(0));
		$model->user_id = 101;
		$model->type = IssueUser::TYPE_VICTIM;
		$this->tester->assertFalse(Yii::$app->authManager->checkAccess(101, Customer::ROLE_VICTIM));
		$this->tester->assertTrue($model->save());
		$this->tester->assertTrue(Yii::$app->authManager->checkAccess(101, Customer::ROLE_VICTIM));
		$this->tester->assertTrue(Yii::$app->authManager->checkAccess(101, Customer::PERMISSION_ISSUE));
	}

	public function testLinkUserWithAlreadyHasRole() {
		Yii::$app->authManager->assign(Yii::$app->authManager->getRole(Customer::ROLE_VICTIM), 101);
		$model = new IssueUserForm();
		$model->setIssue($this->grabIssue(0));
		$model->user_id = 101;
		$model->type = IssueUser::TYPE_VICTIM;
		$this->tester->assertTrue($model->save());
		$this->tester->assertTrue(Yii::$app->authManager->checkAccess(101, Customer::ROLE_VICTIM));
	}

	public function testLinkUserWithAlreadyHasPermission() {
		Yii::$app->authManager->assign(Yii::$app->authManager->getPermission(Customer::PERMISSION_ISSUE), 101);
		$model = new IssueUserForm();
		$model->setIssue($this->grabIssue(0));
		$model->user_id = 101;
		$model->type = IssueUser::TYPE_VICTIM;
		$this->tester->assertTrue($model->save());
		$this->tester->assertTrue(Yii::$app->authManager->checkAccess(101, Customer::PERMISSION_ISSUE));
	}

	public function testSetCustomer() {
		$model = new IssueUserForm();
		$model->setIssue($this->grabIssue(0));
		$model->user_id = 101;
		$model->type = IssueUser::TYPE_CUSTOMER;
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('As role is invalid.', $model->getFirstError('type'));
	}

	public function testSetLawyer() {
		$model = new IssueUserForm();
		$model->setIssue($this->grabIssue(0));
		$model->user_id = 101;
		$model->type = IssueUser::TYPE_LAWYER;
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('As role is invalid.', $model->getFirstError('type'));
	}

	public function testEmpty() {
		$model = new IssueUserForm();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('As role cannot be blank.', $model->getFirstError('type'));
		$this->tester->assertSame('User Id cannot be blank.', $model->getFirstError('user_id'));
		$this->tester->assertSame('Issue cannot be blank.', $model->getFirstError('issue_id'));
	}

	protected function grabIssue(int $fixtureId): ?Issue {
		return $this->tester->grabFixture('issue', $fixtureId);
	}

}
