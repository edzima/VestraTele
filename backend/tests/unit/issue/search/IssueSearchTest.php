<?php

namespace backend\tests\unit\issue\search;

use backend\modules\issue\models\search\IssueSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\settlement\PayedInterface;
use common\tests\_support\UnitSearchModelTrait;
use Yii;

/**
 * @property IssueSearch $model
 */
class IssueSearchTest extends Unit {

	use UnitSearchModelTrait;

	public static bool $hasFixture = false;

	public function _before(): void {
		$this->getModule('Yii2')->_reconfigure(['cleanup' => false]);
		if (!static::$hasFixture) {
			$this->tester->haveFixtures(
				array_merge(
					IssueFixtureHelper::fixtures(),
					IssueFixtureHelper::customerAddress(),
					IssueFixtureHelper::note(),
					SettlementFixtureHelper::settlement(),
					SettlementFixtureHelper::pay(),
				)
			);
			static::$hasFixture = true;
		}
		$this->model = $this->createModel();
		parent::_before();
	}

	public function _after() {
		parent::_after();
		Yii::$app->db->close();
	}

	protected function createModel(): IssueSearch {
		return new IssueSearch();
	}

	public function testEmpty(): void {
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->isArchived());
		}
	}

	public function testWithArchiveAsParams(): void {
		$models = $this->getModels(['withArchive' => 1]);
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->isArchived());
		}
	}

	public function testWithArchive(): void {
		$this->model->withArchive = true;
		$withArchives = $this->getModels();
		$this->model->withArchive = false;
		$withoutArchives = $this->getModels();

		$this->tester->assertNotEmpty($withArchives);
		$this->tester->assertNotEmpty($withoutArchives);
		$this->tester->assertNotSameSize($withArchives, $withoutArchives);

		$archives = array_filter($withArchives, static function (Issue $issue): bool {
			return $issue->isArchived();
		});

		$this->tester->assertNotEmpty($archives);
	}

	public function testForId(): void {
		$this->model->issue_id = 1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		$issue = reset($models);
		$this->tester->assertSame(1, $issue->getIssueId());
	}

	public function testCustomerAddress(): void {
		$this->model->withArchive = true;
		$this->model->addressSearch->postal_code = '43-310';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame('43-310', $model->customer->homeAddress->postal_code);
		}
	}

	public function testForType(): void {
		$this->model->type_id = 1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->type_id);
		}

		$this->model->type_id = 2;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(2, $model->type_id);
		}
	}

	public function testForStage(): void {
		$this->model->stage_id = 1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->stage_id);
		}

		$this->model->stage_id = 2;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(2, $model->stage_id);
		}
	}

	public function testForSigningAt(): void {
		$this->model->signedAtFrom = '2019-01-01';
		$this->model->signedAtTo = '2020-01-01';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNotEmpty($model->signing_at);
			$this->tester->assertGreaterThanOrEqual(strtotime($this->model->signedAtFrom), strtotime($model->signing_at));
			$this->tester->assertLessThanOrEqual(strtotime($this->model->signedAtTo), strtotime($model->signing_at));
		}
	}

	public function testExcludedStages(): void {
		$this->model->excludedStages = [1];
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNotSame(1, $model->stage_id);
		}
		$this->model->excludedStages = [1, 2];
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNotSame(1, $model->stage_id);
			$this->tester->assertNotSame(2, $model->stage_id);
		}
	}

	public function testExcludedTypes(): void {
		$this->model->excludedTypes = [1];
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNotSame(1, $model->type_id);
		}
		$this->model->excludedTypes = [1, 2];
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNotSame(1, $model->type_id);
			$this->tester->assertNotSame(2, $model->type_id);
		}
	}

	public function testWithTelemarkerAsEmpty(): void {
		$this->model->onlyWithTelemarketers = null;
		$models = $this->getModels();
		$tele = array_filter($models, function (Issue $model): bool {
			return $model->tele !== null;
		});
		$this->tester->assertNotEmpty($tele);

		$this->model->onlyWithTelemarketers = '';
		$models = $this->getModels();
		$tele = array_filter($models, function (Issue $model): bool {
			return $model->tele !== null;
		});
		$this->tester->assertNotEmpty($tele);
	}

	public function testWithTelemarkerAsEnable(): void {
		$this->model->onlyWithTelemarketers = '1';
		$models = $this->getModels();
		$tele = array_filter($models, function (Issue $model): bool {
			return $model->tele !== null;
		});
		$this->tester->assertNotEmpty($tele);

		$this->model->onlyWithTelemarketers = true;
		$models = $this->getModels();
		$tele = array_filter($models, function (Issue $model): bool {
			return $model->tele !== null;
		});
		$this->tester->assertNotEmpty($tele);
	}

	public function testWithTelemarkerAsDisable(): void {
		$this->model->onlyWithTelemarketers = '0';
		$models = $this->getModels();
		$tele = array_filter($models, function (Issue $model): bool {
			return $model->tele !== null;
		});
		$this->tester->assertEmpty($tele);

		$this->model->onlyWithTelemarketers = false;
		$models = $this->getModels();
		$tele = array_filter($models, function (Issue $model): bool {
			return $model->tele !== null;
		});
		$this->tester->assertEmpty($tele);
	}

	public function testCustomerLastname(): void {
		$this->model->customerName = 'Lars';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertStringContainsString('Lars', $model->getIssueModel()->customer->profile->lastname);
		}
	}

	public function testForAgent(): void {
		$this->model->agent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->agent->id);
		}

		$this->model->agent_id = UserFixtureHelper::AGENT_AGNES_MILLER;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_AGNES_MILLER, $model->agent->id);
		}
	}

	public function testForLawyer(): void {
		$this->model->lawyer_id = UserFixtureHelper::LAWYER_1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::LAWYER_1, $model->lawyer->id);
		}

		$this->model->lawyer_id = UserFixtureHelper::LAWYER_2;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::LAWYER_2, $model->lawyer->id);
		}
	}

	public function testForTele(): void {
		$this->model->tele_id = UserFixtureHelper::TELE_1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::TELE_1, $model->tele->id);
		}

		$this->model->tele_id = UserFixtureHelper::TELE_2;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::TELE_2, $model->tele->id);
		}
	}

	public function testCustomerWithAgent(): void {
		$this->model->agent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->model->customerName = 'Lar';
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->agent->id);
			$this->tester->assertStringContainsString('Lar', $model->customer->getFullName());
		}
	}

	public function testForAgentAndLawyer(): void {
		$this->model->agent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->model->lawyer_id = UserFixtureHelper::LAWYER_1;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->agent->id);
			$this->tester->assertSame(UserFixtureHelper::LAWYER_1, $model->lawyer->id);
		}
	}

	public function testOnlyWithPayedPays(): void {
		$this->tester->assertFalse($this->model->onlyWithPayedPay);
		$this->model->onlyWithPayedPay = true;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);

		foreach ($models as $model) {
			/** @var Issue $model */
			$payed = array_filter($model->pays, static function (PayedInterface $pay) {
				return $pay->isPayed();
			});
			$this->tester->assertNotEmpty($payed);
		}
	}

	public function testWithSettlements(): void {
		$this->tester->assertNull($this->model->onlyWithSettlements);
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);

		$withSettlements = 0;
		$withoutSettlements = 0;
		foreach ($models as $model) {
			if (empty($model->payCalculations)) {
				$withoutSettlements++;
			} else {
				$withSettlements++;
			}
		}
		$this->tester->assertGreaterThan(0, $withoutSettlements);
		$this->tester->assertGreaterThan(0, $withSettlements);
		$allCount = count($models);
		$this->tester->assertSame($allCount, $withSettlements + $withoutSettlements);

		$this->model->onlyWithSettlements = true;
		$models = $this->getModels();
		$this->tester->assertCount($withSettlements, $models);

		$this->model->onlyWithSettlements = false;
		$models = $this->getModels();
		$this->tester->assertCount($withoutSettlements, $models);

		$this->model->onlyWithSettlements = true;
		$this->model->onlyWithPayedPay = true;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$hasPayed = false;
			foreach ($model->pays as $pay) {
				if (!$hasPayed && $pay->isPayed()) {
					$hasPayed = true;
					break;
				}
			}
			$this->tester->assertTrue($hasPayed);
		}
		$this->tester->assertCount($withSettlements, $models);

		$this->model->onlyWithPayedPay = true;
		$this->model->onlyWithSettlements = false;
		$this->tester->assertEmpty($this->getModels());
	}

	public function testOnlyPinnedNotesFilter(): void {
		$this->model->noteFilter = IssueSearch::NOTE_ONLY_PINNED;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertNotEmpty(IssueNote::pinnedNotesFilter($model->issueNotes));
		}
	}

	public function testOnlyWithAllPayed(): void {
		$this->tester->assertFalse($this->model->withArchiveOnAllPayedPay);
		$this->getModels([
			'onlyWithAllPayedPay' => 1,
			'withArchiveOnAllPayedPay' => 1,
		]);
		$this->tester->assertFalse($this->model->onlyWithAllPayedPay);
		$this->tester->assertFalse($this->model->withArchiveOnAllPayedPay);

		$this->model->setScenario(IssueSearch::SCENARIO_ALL_PAYED);
		$models = $this->getModels(['onlyWithAllPayedPay' => 1]);
		$this->tester->assertTrue($this->model->onlyWithAllPayedPay);
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->getIssueModel()->isArchived());
			$this->tester->assertSame(0, (int) $model->getPays()->onlyUnpaid()->count());
		}

		$this->model->withArchiveOnAllPayedPay = true;
		$models = $this->getModels(['onlyWithAllPayedPay' => 1]);

		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->getIssueModel()->isArchived());
			$this->tester->assertSame(0, (int) $model->getPays()->onlyUnpaid()->count());
		}

		$this->model->withArchive = true;
		$models = $this->getModels(['onlyWithAllPayedPay' => 1]);
		$this->tester->assertNotEmpty($models);
		$archived = false;
		foreach ($models as $model) {
			/** @var $model Issue */
			$this->tester->assertSame(0, (int) $model->getPays()->onlyUnpaid()->count());
			if ($model->isArchived()) {
				$archived = true;
			}
		}
		$this->tester->assertTrue($archived);
	}

	public function testWithSettlementsAndAgent(): void {
		$this->model->onlyWithSettlements = true;
		$this->model->agent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(UserFixtureHelper::AGENT_PETER_NOWAK, $model->agent->id);
		}
	}

	/**
	 * @param array $params
	 * @return Issue[]
	 */
	private function getModels(array $params = []): array {
		return $this->search($params)->getModels();
	}
}
