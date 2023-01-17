<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueStageForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssueStage;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class IssueStageFormTest extends Unit {

	use UnitModelTrait;

	private IssueStageForm $model;
	private IssueFixtureHelper $issueFixtureHelper;

	public function _before() {
		parent::_before();
		$this->issueFixtureHelper = new IssueFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::entityResponsible(),
			IssueFixtureHelper::stageAndTypesFixtures()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name cannot be blank.', 'name');
		$this->thenSeeError('Shortname cannot be blank.', 'short_name');
	}

	private function giveModel(array $config = []): void {
		$this->model = new IssueStageForm($config);
	}

	public function testSimpleSave(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
		]);

		$this->thenSuccessSave();
		$this->thenSeeStage([
			'name' => 'Test Stage',
			'short_name' => 'TS',
		]);
	}

	private function thenSeeStage(array $attributes): void {
		$this->tester->seeRecord(IssueStage::class, $attributes);
	}

	public function testNotUniqueName(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
		]);

		$this->thenSuccessSave();

		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS2',
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name "Test Stage" has already been taken.', 'name');
	}

	public function testNotUniqueShortName(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
		]);

		$this->thenSuccessSave();

		$this->giveModel([
			'name' => 'Test Stage 2',
			'short_name' => 'TS',
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Shortname "TS" has already been taken.', 'short_name');
	}

	public function getModel(): Model {
		return $this->model;
	}

}
