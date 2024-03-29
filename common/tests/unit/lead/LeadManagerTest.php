<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\components\LeadManager;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\models\LeadUser;
use common\tests\helpers\LeadFactory;
use common\tests\unit\Unit;

class LeadManagerTest extends Unit {

	protected const LEAD_MODEL = Lead::class;

	private LeadManager $leadManager;
	private LeadInterface $lead;

	private ?ActiveLead $pushed = null;

	public function _before() {
		$this->giveLeadComponent();
		parent::_before(); // TODO: Change the autogenerated stub
	}

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	private function giveLeadComponent(): void {
		$this->leadManager = new LeadManager([
			'model' => static::LEAD_MODEL,
		]);
	}

	public function testPushEmpty(): void {
		$this->giveLead([]);

		$this->whenPush();

		$this->thenUnsuccessPush();
	}

	public function testPush(): void {
		$this->giveLead([
			'name' => 'Test push',
			'source_id' => 1,
			'status_id' => 1,
			'phone' => '789-185-145',
		]);

		$this->whenPush();
		$this->thenSuccessPush();
		$this->thenSeeLead();
	}

	public function testPushWithUser(): void {
		$this->giveLead([
			'name' => 'Test push with user',
			'source_id' => 1,
			'status_id' => 1,
			'phone' => '789-185-145',
			'owner_id' => 1,
		]);

		$this->whenPush();
		$this->thenSuccessPush();

		$this->thenSeeLeadUser(LeadUser::TYPE_OWNER, 1);

		$this->leadManager->onlyForUser = false;

		$this->tester->assertFalse($this->pushed->isForUser(null));
		$this->tester->assertTrue($this->pushed->isForUser(1));
		$this->tester->assertFalse($this->pushed->isForUser(2));

		$this->tester->assertTrue($this->leadManager->isForUser($this->pushed, null));
		$this->tester->assertTrue($this->leadManager->isForUser($this->pushed, 1));
		$this->tester->assertTrue($this->leadManager->isForUser($this->pushed, 2));

		$this->leadManager->onlyForUser = true;
		$this->tester->assertFalse($this->leadManager->isForUser($this->pushed, null));
		$this->tester->assertTrue($this->leadManager->isForUser($this->pushed, 1));
		$this->tester->assertFalse($this->leadManager->isForUser($this->pushed, 2));
	}

	public function testPushSourceWithOwner(): void {
		$sourceId = $this->tester->haveRecord(LeadSource::class, [
			'type_id' => 1,
			'owner_id' => 1,
			'name' => 'Some owner source',
		]);
		$this->giveLead([
			'name' => 'Source With Owner',
			'source_id' => $sourceId,
			'phone' => '789-185-145',
		]);

		$this->whenPush();
		$this->thenSuccessPush();
		$this->thenSeeLeadUser(LeadUser::TYPE_OWNER, 1);
	}

	public function testDoubleSamePush(): void {

		$this->giveLead([
			'source_id' => 1,
			'status_id' => 1,
			'phone' => '789-185-145',
			'name' => 'Double Same Push',
		]);

		$this->whenPush();
		$first = $this->pushed;
		$this->thenSuccessPush();
		$this->whenPush();
		$this->thenSuccessPush();
		$second = $this->pushed;
		$this->tester->assertNotSame($first->getId(), $second->getId());
	}

	public function testDoubleSamePhone(): void {
		$this->giveLead([
			'source_id' => 1,
			'status_id' => 1,
			'phone' => '789-185-145',
			'name' => 'First Contact from Source 1.',
		]);
		$this->whenPush();
		$this->thenSuccessPush();
		$this->tester->assertEmpty($this->pushed->getSameContacts());

		$this->giveLead([
			'source_id' => 2,
			'status_id' => 1,
			'phone' => '789-185-145',
			'name' => 'Second Contact from Source 2.',
		]);
		$this->whenPush();
		$this->thenSuccessPush();
		$leads = $this->pushed->getSameContacts();
		$this->tester->assertCount(1, $leads);
		$lead = reset($leads);
		$this->tester->assertSame('First Contact from Source 1.', $lead->getName());
	}

	protected function giveLead(array $data): void {
		if (!isset($data['status_id'])) {
			$data['status_id'] = LeadStatusInterface::STATUS_NEW;
		}
		$this->lead = LeadFactory::createLead($data);
	}

	private function whenPush(): void {
		$this->pushed = $this->leadManager->pushLead($this->lead);
	}

	private function thenSuccessPush(): void {
		$this->tester->assertNotNull($this->pushed);
		$this->tester->assertNotNull($this->leadManager->findById($this->pushed->getId()));
	}

	private function thenUnsuccessPush(): void {
		$this->tester->assertNull($this->pushed);
	}

	private function thenSeeLead(): void {
		$this->tester->assertNotEmpty($this->leadManager->findByLead($this->lead));
	}

	private function thenSeeLeadUser(string $type, string $userId) {
		$this->tester->seeRecord(LeadUser::class, [
			'type' => $type,
			'lead_id' => $this->pushed->getId(),
			'user_id' => $userId,
		]);
	}

}
