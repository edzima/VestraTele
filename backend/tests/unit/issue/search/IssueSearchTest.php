<?php

namespace backend\tests\unit\issue\search;

use backend\modules\issue\models\search\IssueSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use yii\data\ActiveDataProvider;

class IssueSearchTest extends Unit {

	/** @var IssueSearch */
	protected IssueSearch $model;

	public static bool $hasFixture = false;

	public function _before(): void {
		$this->getModule('Yii2')->_reconfigure(['cleanup' => false]);
		if (!static::$hasFixture) {
			$this->tester->haveFixtures(IssueFixtureHelper::fixtures());
			static::$hasFixture = true;
		}
		$this->model = $this->createModel();
		parent::_before();
	}

	protected function createModel(): IssueSearch {
		return new IssueSearch();
	}

	public function testWithoutSearchParams(): void {
		$this->assertTotalCount(4);
	}

	public function testWithArchiveAsParams(): void {
		$this->assertTotalCount(4);
	}

	public function testWithArchive(): void {
		$this->model->withArchive = true;
		$this->assertTotalCount(5);
	}

	public function testForId(): void {
		$this->assertTotalCount(0, ['issue_id' => 200]);
		$this->assertTotalCount(1, ['issue_id' => 1]);
	}

	public function testForType(): void {
		$this->assertTotalCount(2, ['type_id' => 1]);
		$this->assertTotalCount(2, ['type_id' => 2]);
	}

	public function testForStage(): void {
		$this->assertTotalCount(2, ['stage_id' => 1]);
		$this->assertTotalCount(2, ['stage_id' => 2]);
	}

	public function testExcludedStages(): void {
		$this->assertTotalCount(2, ['excludedStages' => [2]]);
		$this->assertTotalCount(2, ['excludedStages' => [1]]);
	}

	public function testCustomerLastname(): void {
		$this->assertTotalCount(2, ['customerLastname' => 'Lars']);
	}

	public function testForAgent(): void {
		$this->assertTotalCount(2, ['agent_id' => 300]);
	}

	public function testForLawyer(): void {
		$this->assertTotalCount(1, ['lawyer_id' => 200]);
	}

	public function testForTele(): void {
		$this->assertTotalCount(1, ['tele_id' => 400]);
	}

	public function testCustomerWithAgent(): void {
		$this->assertTotalCount(1, ['agent_id' => 300, 'customerLastname' => 'Lar']);
	}


	public function testForAgentAndLawyer(): void {
		$this->assertTotalCount(1, ['agent_id' => 300, 'lawyer_id' => 200]);
	}


	protected function assertTotalCount(int $count, array $params = []): void {
		$this->tester->assertSame($count, $this->search($params)->getTotalCount());
	}

	protected function search(array $params = []): ActiveDataProvider {
		return $this->model->search(['IssueSearch' => $params]);
	}

}
