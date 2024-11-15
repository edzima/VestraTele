<?php

namespace common\tests\unit\provision;

use common\components\provision\exception\MissingParentProvisionUserException;
use common\components\provision\Provisions;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\provision\Provision;
use common\models\provision\ProvisionUserData;
use common\models\user\User;
use common\tests\unit\Unit;
use Decimal\Decimal;
use yii\base\InvalidConfigException;

class ProvisionComponentTest extends Unit {

	private SettlementFixtureHelper $settlementFixture;
	private ProvisionFixtureHelper $provisionFixture;
	private Provisions $provision;

	public function _before() {
		parent::_before();
		$this->provision = new Provisions();
		$this->provisionFixture = new ProvisionFixtureHelper($this->tester);
		$this->settlementFixture = new SettlementFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			['agent' => UserFixtureHelper::agent()],
			ProvisionFixtureHelper::provision(),
			ProvisionFixtureHelper::user(),
			ProvisionFixtureHelper::type(),
			SettlementFixtureHelper::pay(codecept_data_dir() . 'provision/'),
			SettlementFixtureHelper::settlement(codecept_data_dir() . 'provision/'),
			SettlementFixtureHelper::type(),
		);
	}

	public function testBatchInsertEmptyArray(): void {
		$this->tester->assertSame(0, $this->batchInsert([]));
	}

	public function testBatchInsertSingleRow(): void {
		$provision = $this->createProvisionData(1000);
		$this->tester->assertSame(1, $this->batchInsert([$provision]));
		$this->thenSeeProvision($provision);
	}

	public function testBatchInsertMultipleData(): void {
		$provision1 = $this->createProvisionData(1000);
		$provision2 = $this->createProvisionData(1000);

		$this->tester->assertSame(2, $this->batchInsert([$provision1, $provision2]));
		$this->thenSeeProvision($provision1);
		$this->thenSeeProvision($provision2);
	}

	public function testRemoveForPay(): void {

		$this->batchInsert([
			$this->createProvisionData(200, 1),
			$this->createProvisionData(200, 2),
			$this->createProvisionData(200, 2),
			$this->createProvisionData(300, 3),
		]);

		$this->provision->removeForPays([1]);

		$this->thenDontSeeProvision(['pay_id' => 1]);
		$this->thenSeeProvision(['pay_id' => 2]);
		$this->thenSeeProvision(['pay_id' => 3]);
	}

	public function testRemoveForPaysWhenEmptyArray(): void {
		$this->batchInsert([
			$this->createProvisionData(200, 1),
			$this->createProvisionData(200, 2),
			$this->createProvisionData(200, 2),
			$this->createProvisionData(300, 3),
		]);

		$this->provision->removeForPays([]);
		$this->thenSeeProvision(['pay_id' => 1]);
		$this->thenSeeProvision(['pay_id' => 2]);
		$this->thenSeeProvision(['pay_id' => 3]);
	}

	public function testGenerateProvisionsDataWithoutType(): void {
		$provisionUserData = new ProvisionUserData($this->tester->grabFixture('agent', 'with-childs'));

		$this->tester->expectThrowable(InvalidConfigException::class, function () use ($provisionUserData) {
			$this->provision->generateProvisionsData($provisionUserData, [
				1 => new Decimal(100),
				2 => new Decimal(200),
			]);
		});
	}

	public function testGenerateProvisionsDataWithoutParentProvision(): void {
		$provisionUserData = new ProvisionUserData(User::findOne(UserFixtureHelper::AGENT_AGNES_MILLER));
		$provisionUserData->type = $this->provisionFixture->grabType();

		$this->tester->expectThrowable(MissingParentProvisionUserException::class, function () use ($provisionUserData) {
			$this->provision->generateProvisionsData($provisionUserData, [
				1 => new Decimal(100),
				2 => new Decimal(200),
			]);
		});
	}

	public function testGenerateProvisionsDataWithSelfProvision(): void {
		$user = $this->tester->grabFixture('agent', 'without-parent-and-childs');
		$provisionUserData = new ProvisionUserData($user);
		$provisionUserData->type = $this->provisionFixture->grabType();

		$this->provisionFixture->haveProvisionUser(20, [
			'from_user_id' => $user->id,
			'to_user_id' => $user->id,
			'type_id' => $provisionUserData->type->id,
		]);

		$data = $this->provision->generateProvisionsData($provisionUserData, [
			1 => new Decimal(100),
			2 => new Decimal(200),
		]);

		$this->tester->assertCount(2, $data);
		$this->tester->assertSame(2, $this->batchInsert($data));
		$this->thenSeeProvision([
			'pay_id' => 1,
			'value' => 20,
			'from_user_id' => $user->id,
			'to_user_id' => $user->id,
			'type_id' => $provisionUserData->type->id,
		]);

		$this->thenSeeProvision([
			'pay_id' => 2,
			'value' => 40,
			'from_user_id' => $user->id,
			'to_user_id' => $user->id,
			'type_id' => $provisionUserData->type->id,
		]);
	}

	public function testSettlement(): void {
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users()
		));
		$settlement = $this->settlementFixture->grabSettlement('accident-honorarium-single-pay-not-payed');
		$agent = $settlement->issue->agent;

		$this->provisionFixture->haveProvisionUser(30, [
			'from_user_id' => $agent->id,
			'to_user_id' => $agent->id,
			'type_id' => $this->provisionFixture->grabType('agent-percent-25')->id,
		]);

		$this->provisionFixture->haveProvisionUser(5, [
			'from_user_id' => $agent->id,
			'to_user_id' => $agent->boss,
			'type_id' => $this->provisionFixture->grabType('agent-percent-25')->id,
		]);

		$tele = $settlement->issue->tele;
		$this->provisionFixture->haveProvisionUser(5, [
			'from_user_id' => $tele->id,
			'to_user_id' => $tele->id,
			'type_id' => $this->provisionFixture->grabType('tele-percent-5')->id,
		]);

		$this->assertSame(5, $this->provision->settlement($settlement));
	}

	private function thenSeeProvision(array $data): void {
		$this->tester->seeRecord(Provision::class, $data);
	}

	private function thenDontSeeProvision(array $data): void {
		$this->tester->dontSeeRecord(Provision::class, $data);
	}

	private function createProvisionData(string $value,
		int $pay_id = 1,
		int $from_user_id = UserFixtureHelper::AGENT_PETER_NOWAK,
		int $to_user_id = UserFixtureHelper::AGENT_PETER_NOWAK,
		int $type_id = 1): array {
		return [
			'pay_id' => $pay_id,
			'value' => $value,
			'to_user_id' => $to_user_id,
			'from_user_id' => $from_user_id,
			'type_id' => $type_id,
			'percent' => null,
		];
	}

	private function batchInsert(array $provisionsData): int {
		return $this->provision->batchInsert($provisionsData);
	}
}
