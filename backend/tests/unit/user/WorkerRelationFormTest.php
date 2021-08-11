<?php

namespace backend\tests\unit\user;

use backend\modules\user\models\WorkerRelationForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\user\UserRelation;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class WorkerRelationFormTest extends Unit {

	use UnitModelTrait;

	private WorkerRelationForm $model;

	public function _before() {
		parent::_before();
		$this->model = new WorkerRelationForm();
	}

	public function _fixtures(): array {
		return [
			'agent' => UserFixtureHelper::agent(),
			'relation' => UserFixtureHelper::relation(),
		];
	}

	public function testEmpty(): void {
		$this->thenUnsuccessValidate();
		$this->thenSeeError('User cannot be blank.', 'userId');
		$this->thenSeeError('To Users cannot be blank.', 'toUsersIds');

		$this->thenSeeError('Type cannot be blank.', 'type');
	}

	public function testSupervisorType(): void {
		$this->model->type = UserRelation::TYPE_SUPERVISOR;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type is invalid.', 'type');
	}

	public function testInvalidUserId(): void {
		$this->model->userId = UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('User is invalid.', 'userId');
	}

	public function testInvalidToUsersIds(): void {
		$this->model->toUsersIds = [UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('To Users is invalid.', 'toUsersIds');
	}

	public function testSingleSaveAndChange(): void {
		$this->model->type = UserRelation::TYPE_PREVIEW_ISSUES;
		$this->model->userId = UserFixtureHelper::AGENT_AGNES_MILLER;
		$this->model->toUsersIds = [UserFixtureHelper::AGENT_PETER_NOWAK];
		$this->thenSuccessSave();
		$this->thenSeeRelation(UserFixtureHelper::AGENT_PETER_NOWAK);
		$this->model->toUsersIds = [UserFixtureHelper::AGENT_EMILY_PAT];
		$this->thenSuccessSave();
		$this->thenDontSeeRelation(UserFixtureHelper::AGENT_PETER_NOWAK);
		$this->thenSeeRelation(UserFixtureHelper::AGENT_EMILY_PAT);
	}

	public function getModel(): Model {
		return $this->model;
	}

	private function thenSeeRelation(int $to_user_id) {
		return $this->tester->seeRecord(UserRelation::class, [
			'type' => $this->model->type,
			'user_id' => $this->model->userId,
			'to_user_id' => $to_user_id,
		]);
	}

	private function thenDontSeeRelation(int $to_user_id) {
		return $this->tester->dontSeeRecord(UserRelation::class, [
			'type' => $this->model->type,
			'user_id' => $this->model->userId,
			'to_user_id' => $to_user_id,
		]);
	}
}
