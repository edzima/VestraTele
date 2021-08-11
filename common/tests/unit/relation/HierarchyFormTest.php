<?php

namespace common\tests\unit\relation;

use common\components\RelationComponent;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\relation\HierarchyForm;
use common\models\user\UserRelation;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class HierarchyFormTest extends Unit {

	use UnitModelTrait;

	private HierarchyForm $model;
	private RelationComponent $relationComponent;

	public function _before() {
		parent::_before();
		$this->relationComponent = new RelationComponent([
			'relationModel' => UserRelation::class,
		]);
	}

	public function _fixtures(): array {
		return [
			'agent' => UserFixtureHelper::agent(),
			'relation' => UserFixtureHelper::relation(),
		];
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenUnsuccessSave();
		$this->thenSeeError('ID cannot be blank.', 'id');
	}

	public function testRemoveParent(): void {
		$this->giveModel();
		$this->model->id = UserFixtureHelper::AGENT_EMILY_PAT;
		$this->model->parent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->thenSuccessSave();
		$this->thenSeeRelation(UserFixtureHelper::AGENT_PETER_NOWAK, UserFixtureHelper::AGENT_EMILY_PAT);
		$this->model->parent_id = null;
		$this->thenSuccessSave();
		$this->thenDontSeeRelation(UserFixtureHelper::AGENT_PETER_NOWAK, UserFixtureHelper::AGENT_EMILY_PAT);
	}

	public function testChangeParent(): void {
		$this->giveModel();
		$this->model->id = UserFixtureHelper::AGENT_EMILY_PAT;
		$this->model->parent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->thenSuccessSave();
		$this->thenSeeRelation(UserFixtureHelper::AGENT_PETER_NOWAK, UserFixtureHelper::AGENT_EMILY_PAT);
		$this->model->parent_id = UserFixtureHelper::AGENT_AGNES_MILLER;
		$this->thenSuccessSave();
		$this->thenDontSeeRelation(UserFixtureHelper::AGENT_PETER_NOWAK, UserFixtureHelper::AGENT_EMILY_PAT);
		$this->thenSeeRelation(UserFixtureHelper::AGENT_AGNES_MILLER, UserFixtureHelper::AGENT_EMILY_PAT);
	}

	public function testParentSameAsId(): void {
		$this->giveModel();
		$this->model->id = UserFixtureHelper::AGENT_EMILY_PAT;
		$this->model->parent_id = UserFixtureHelper::AGENT_EMILY_PAT;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('ID must not be equal to "Parent".', 'id');
	}

	public function testParentAsSelfChild(): void {
		$this->giveModel();
		$this->model->id = UserFixtureHelper::AGENT_AGNES_MILLER;
		$this->model->parent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->thenSuccessSave();
		$this->giveModel();
		$this->model->id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->model->parent_id = UserFixtureHelper::AGENT_AGNES_MILLER;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Parent cannot be from childes.', 'parent_id');
	}

	public function getModel(): Model {
		return $this->model;
	}

	private function thenSeeRelation(int $fromUserId, int $toUserId): void {
		$this->tester->seeRecord(UserRelation::class, [
			'user_id' => $fromUserId,
			'to_user_id' => $toUserId,
			'type' => UserRelation::TYPE_SUPERVISOR,
		]);
	}

	private function thenDontSeeRelation(int $fromUserId, int $toUserId): void {
		$this->tester->dontSeeRecord(UserRelation::class, [
			'user_id' => $fromUserId,
			'to_user_id' => $toUserId,
			'type' => UserRelation::TYPE_SUPERVISOR,
		]);
	}

	private function giveModel(): void {
		$this->model = new HierarchyForm($this->relationComponent);
	}
}
