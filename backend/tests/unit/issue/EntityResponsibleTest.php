<?php

namespace backend\tests\unit\issue;

use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\issue\EntityResponsibleFixture;
use common\models\entityResponsible\EntityResponsible;

class EntityResponsibleTest extends Unit {

	public function _fixtures(): array {
		return IssueFixtureHelper::entityResponsible();
	}

	public function testDuplicateName(): void {
		$model = new EntityResponsible(['name' => 'Alianz']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name "Alianz" has already been taken.', $model->getFirstError('name'));
	}

	public function testValidCreate(): void {
		$model = new EntityResponsible(['name' => 'MOPS']);
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(EntityResponsible::class, [
			'name' => 'MOPS',
		]);
	}
}
