<?php

namespace common\tests\unit\provision\search;

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserSearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

/**
 * @property ProvisionUserSearch $model
 */
class ProvisionUserSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->model = $this->createModel();
		$this->tester->haveFixtures(array_merge(
			ProvisionFixtureHelper::user(),
			ProvisionFixtureHelper::type()
		));
		parent::_before();
	}

	public function testOnlySelf(): void {
		$models = $this->search(['onlySelf' => true])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertTrue($model->isSelf());
		}
	}

	public function testFromUser(): void {
		$models = $this->search(['from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertSame($model->from_user_id, UserFixtureHelper::AGENT_PETER_NOWAK);
		}

		$models = $this->search(['from_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertSame($model->from_user_id, UserFixtureHelper::AGENT_AGNES_MILLER);
		}

		$models = $this->search([
			'from_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER,
			'onlySelf' => true,
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertSame($model->from_user_id, UserFixtureHelper::AGENT_AGNES_MILLER);
			$this->tester->assertTrue($model->isSelf());
		}
	}

	public function testToUser(): void {
		$models = $this->search(['to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertSame($model->to_user_id, UserFixtureHelper::AGENT_PETER_NOWAK);
		}

		$models = $this->search(['to_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertSame($model->to_user_id, UserFixtureHelper::AGENT_AGNES_MILLER);
		}
	}

	public function testOverwritten(): void {
		$models = $this->search(['overwritten' => true])->getModels();
		$this->tester->assertNotEmpty($models);
		$overwrittenCount = count($models);
		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertTrue($model->getIsOverwritten());
		}

		$models = $this->search(['overwritten' => false])->getModels();
		$this->tester->assertNotEmpty($models);
		$dontOverwrittenCount = count($models);

		foreach ($models as $model) {
			/** @var ProvisionUser $model */
			$this->tester->assertFalse($model->getIsOverwritten());
		}
		$this->tester->assertSame($this->search(['overwritten' => null])->getTotalCount(), $overwrittenCount + $dontOverwrittenCount);
	}

	protected function createModel(): ProvisionUserSearch {
		return new ProvisionUserSearch();
	}
}
