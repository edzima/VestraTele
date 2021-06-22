<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\searches\LeadSearch;
use common\tests\unit\Unit;

class LeadSearchTest extends Unit {

	private LeadSearch $model;
	/** @var ActiveLead[] */
	private ?array $models = null;

	public function _before() {
		parent::_before();
		$this->model = new LeadSearch();
	}

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::source(),
			LeadFixtureHelper::user(),
			LeadFixtureHelper::reports(),
		);
	}

	public function testType(): void {
		$this->model->type_id = 1;
		$models = $this->getSearchModels();

		foreach ($models as $model) {
			$this->assertSame(1, $model->getSource()->getType()->getID());
		}
	}

	public function testStatus(): void {
		$this->model->status_id = LeadStatusInterface::STATUS_NEW;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->assertSame(LeadStatusInterface::STATUS_NEW, $model->getStatusId());
		}

		$this->model->status_id = LeadStatusInterface::STATUS_ARCHIVE;
		$models = $this->getSearchModels(true);
		foreach ($models as $model) {
			$this->assertSame(LeadStatusInterface::STATUS_ARCHIVE, $model->getStatusId());
		}
		$this->model->status_id = 0;
		$this->assertSame(0, $this->model->search()->getTotalCount());
	}

	public function testSource(): void {
		$this->model->source_id = 1;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->assertSame(1, $model->getSourceId());
		}
		$this->model->source_id = 2;
		$models = $this->getSearchModels(true);
		foreach ($models as $model) {
			$this->assertSame(2, $model->getSourceId());
		}
		$this->model->source_id = 0;
		$this->assertSame(0, $this->model->search()->getTotalCount());
	}

	public function testUser(): void {
		$this->model->user_id = 1;
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->assertTrue($model->isForUser(1));
		}
		$this->model->user_id = 2;
		$models = $this->getSearchModels(true);
		foreach ($models as $model) {
			$this->assertTrue($model->IsForUser(2));
		}
		$this->model->user_id = 3;
		$models = $this->getSearchModels(true);
		$this->tester->assertEmpty($models);
	}

	public function testEmail(): void {
		$this->model->email = 'test@lead.com';
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->tester->assertSame('test@lead.com', $model->getEmail());
		}
	}

	public function testPhone(): void {
		$this->model->phone = '777-222-122';
		$models = $this->getSearchModels();
		foreach ($models as $model) {
			$this->tester->assertSame('777-222-122', $model->getPhone());
		}
	}

	public function testProvider(): void {
		$this->model->provider = Lead::PROVIDER_FORM;
		foreach ($this->getSearchModels() as $model) {
			$this->tester->assertSame(Lead::PROVIDER_FORM, $model->getProvider());
		}
	}

	public function testEmptyOnUserScenario(): void {
		$this->model->setScenario(LeadSearch::SCENARIO_USER);
		$this->tester->assertEmpty($this->getSearchModels());
		$this->tester->assertSame('User cannot be blank.', $this->model->getFirstError('user_id'));
	}

	public function testLoadOtherUserOnUserScenario(): void {
		$this->model->setScenario(LeadSearch::SCENARIO_USER);
		$this->model->user_id = 1;
		$this->model->load(['user_id' => 2], '');
		$this->assertSame(1, $this->model->user_id);
	}

	public function testAnswerForQuestionWithPlaceholder(): void {
		$this->model->answers = [
			1 => 'Joh',
		];
		$models = $this->getSearchModels();
		$this->tester->assertNotEmpty(array_filter($models, function (Lead $model): bool {
			return $model->answers[1]->answer === 'John';
		}));
		$this->tester->assertNotEmpty(array_filter($models, function (Lead $model): bool {
			return $model->answers[1]->answer === 'Joh';
		}));

		$this->tester->assertNotEmpty(array_filter($models, function (Lead $model): bool {
			return $model->answers[1]->answer === 'Joanna';
		}));
	}

	public function testAnswersForQuestionsWithPlaceholder(): void {
		$this->model->answers = [
			1 => 'Joh',
			2 => 'Mil',
		];

		$models = $this->getSearchModels();
		//@todo fix multiple answers
		$this->tester->assertCount(1, $models);
		/*
		$this->tester->assertNotEmpty(array_filter($models, function (Lead $model): bool {
			return $model->answers[1]->answer === 'John';
		}));

		$this->assertNotEmpty(array_filter($models, function (Lead $model): bool {
			$answer2 = $model->answers[2] ?? null;
			return $answer2 && $answer2->answer === 'Miller';
		}));
		*/
	}

	/**
	 * @param bool $refresh
	 * @return Lead[]
	 */
	private function getSearchModels(bool $refresh = false): array {
		if ($refresh || $this->models === null) {
			$this->models = $this->model->search()->getModels();
		}
		return $this->models;
	}

}
