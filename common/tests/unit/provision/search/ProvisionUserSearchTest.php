<?php

namespace common\tests\unit\provision\search;

use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\provision\ProvisionUserSearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

class ProvisionUserSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		$this->model = $this->createModel();
		$this->tester->haveFixtures(array_merge(
			ProvisionFixtureHelper::user(),
			ProvisionFixtureHelper::type()
		));
		parent::_before();
	}

	public function testEmpty(): void {
		$this->assertTotalCount(3);
	}

	public function testOnlySelf(): void {
		$this->assertTotalCount(2, ['onlySelf' => true]);
	}

	public function testFromUser(): void {
		$this->assertTotalCount(2, ['from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK]);
		$this->assertTotalCount(1, ['from_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER]);

		$this->assertTotalCount(1, ['from_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK, 'onlySelf' => true]);
		$this->assertTotalCount(1, ['from_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER, 'onlySelf' => true]);
	}

	public function testToUser(): void {
		$this->assertTotalCount(1, ['to_user_id' => UserFixtureHelper::AGENT_PETER_NOWAK]);
		$this->assertTotalCount(2, ['to_user_id' => UserFixtureHelper::AGENT_AGNES_MILLER]);
	}

	public function testOverwritten(): void {
		$this->assertTotalCount(3, ['overwritten' => null]);
		$this->assertTotalCount(2, ['overwritten' => true]);
		$this->assertTotalCount(1, ['overwritten' => false]);
	}

	protected function createModel(): ProvisionUserSearch {
		return new ProvisionUserSearch();
	}
}
