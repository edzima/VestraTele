<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\SummonForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\models\user\Worker;
use common\tests\_support\UnitModelTrait;

class SummonFormTest extends Unit {

	use UnitModelTrait;

	private const DEFAULT_ENTITY_ID = 3;
	private const DEFAULT_CITY_ID = TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA;
	private const DEFAULT_OWNER_ID = UserFixtureHelper::AGENT_PETER_NOWAK;
	private const DEFAULT_CONTRACTOR_ID = UserFixtureHelper::AGENT_AGNES_MILLER;

	private SummonForm $model;

	public function _fixtures(): array {
		$agent = UserFixtureHelper::agent();
		UserFixtureHelper::addPermission($agent, Worker::PERMISSION_SUMMON);
		return array_merge(
			[
				'summon-agents' => $agent,
			],
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::entityResponsible(),
			IssueFixtureHelper::summon()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type cannot be blank.', 'type_id');
		$this->thenSeeError('Title cannot be blank.', 'title');
		$this->thenSeeError('Start at cannot be blank.', 'start_at');
		$this->thenSeeError('Contractor cannot be blank.', 'contractor_id');
		$this->thenSeeError('Issue cannot be blank.', 'issue_id');
		$this->thenSeeError('Entity responsible cannot be blank.', 'entity_id');
		$this->thenSeeError('City cannot be blank.', 'city_id');
	}

	public function testSave(): void {
		$this->giveModel();
		$model = $this->model;
		$model->issue_id = 1;
		$model->type_id = 1;
		$model->start_at = '2020-01-01';
		$model->title = 'Test Unit Summon Title';
		$model->city_id = static::DEFAULT_CITY_ID;
		$model->contractor_id = static::DEFAULT_CONTRACTOR_ID;
		$model->entity_id = static::DEFAULT_ENTITY_ID;

		$this->thenSuccessSave();
		$this->tester->seeRecord(Summon::class, [
			'issue_id' => 1,
			'type_id' => 1,
			'title' => 'Test Unit Summon Title',
			'start_at' => '2020-01-01',
			'entity_id' => static::DEFAULT_ENTITY_ID,
			'contractor_id' => static::DEFAULT_CONTRACTOR_ID,
			'owner_id' => static::DEFAULT_OWNER_ID,
			'city_id' => static::DEFAULT_CITY_ID,
		]);
	}

	public function testDeadlineFromTerm(): void {
		$this->giveModel();
		$model = $this->model;
		$model->issue_id = 1;
		$model->type_id = 1;
		$model->title = 'Summon Title With 5 Days Deadline';
		$model->start_at = '2020-01-01';
		$model->term = SummonForm::TERM_FIVE_DAYS;

		$model->city_id = static::DEFAULT_CITY_ID;
		$model->contractor_id = static::DEFAULT_CONTRACTOR_ID;
		$model->entity_id = static::DEFAULT_ENTITY_ID;
		$this->thenSuccessSave();
		$this->tester->seeRecord(Summon::class, [
			'issue_id' => 1,
			'type_id' => 1,
			'entity_id' => static::DEFAULT_ENTITY_ID,
			'city_id' => static::DEFAULT_CITY_ID,
			'title' => 'Summon Title With 5 Days Deadline',
			'deadline_at' => '2020-01-06',
		]);
	}

	public function testEmptyDeadlineWithTermCustom(): void {
		$this->giveModel();
		$this->model->term = SummonForm::TERM_CUSTOM;
		$this->thenUnsuccessValidate('deadline_at');
		$this->thenSeeError('Deadline At cannot be blank on custom term.', 'deadline_at');
	}

	public function testEmptyDeadlineWithEmptyTerm(): void {

	}

	public function testSetType(): void {
		$this->giveModel();
		$type = new SummonType(['title' => 'Test Title', 'id' => 100, 'term' => SummonForm::TERM_ONE_MONTH]);
		$this->tester->assertEmpty($this->model->title);
		$this->model->setType($type);
		$this->tester->assertSame($type->id, $this->model->type_id);
		$this->tester->assertSame($type->title, $this->model->title);
		$this->tester->assertSame($type->term, $this->model->term);
	}

	private function giveModel(array $config = []) {
		if (!isset($config['owner_id'])) {
			$config['owner_id'] = static::DEFAULT_OWNER_ID;
		}

		$this->model = new SummonForm($config);
	}

	public function getModel(): SummonForm {
		return $this->model;
	}

}
