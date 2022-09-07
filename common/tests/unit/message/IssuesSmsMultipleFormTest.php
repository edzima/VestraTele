<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueUser;
use common\models\message\IssuesSmsMultipleForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class IssuesSmsMultipleFormTest extends Unit {

	use UnitModelTrait;

	private const DEFAULT_OWNER_ID = UserFixtureHelper::AGENT_PETER_NOWAK;

	private IssuesSmsMultipleForm $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::note()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Ids cannot be blank.', 'ids');
		$this->thenSeeError('User Types cannot be blank.', 'userTypes');
		$this->thenDontSeeError('phone');
		$this->thenDontSeeError('phones');
	}

	public function testSend(): void {
		$this->giveModel();
		$this->model->ids = [1];
		$this->model->userTypes = [IssueUser::TYPE_CUSTOMER];
		$this->model->message = __FUNCTION__;
		$this->thenSuccessValidate();
		$this->assertTrue($this->model->send());
	}

	public function testPushJobs(): void {
		$this->giveModel();
		$this->model->ids = [1];
		$this->model->userTypes = [IssueUser::TYPE_CUSTOMER];
		$this->model->message = __FUNCTION__;
		$this->thenSuccessValidate();
		$this->tester->assertNotEmpty($this->model->pushJobs());
	}

	protected function giveModel(array $config = []): void {
		if (!isset($config['owner_id'])) {
			$config['owner_id'] = static::DEFAULT_OWNER_ID;
		}
		$this->model = new IssuesSmsMultipleForm($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}
