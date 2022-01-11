<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadStatusChangeForm;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class LeadStatusChangeFormTest extends Unit {

	use UnitModelTrait;

	private const OWNER_ID = 1;

	private LeadStatusChangeForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::user()
		);
	}

	public function testSingle(): void {
		$this->giveModel(LeadStatusInterface::STATUS_ARCHIVE, [1]);
		$this->thenSuccessSave();
		$this->tester->seeRecord(Lead::class, [
			'id' => 1,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => 1,
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'owner_id' => static::OWNER_ID,
		]);
	}

	public function testMultiple(): void {
		$this->giveModel(LeadStatusInterface::STATUS_ARCHIVE, [1, 2]);
		$this->thenSuccessSave();
		$this->tester->seeRecord(Lead::class, [
			'id' => 1,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$this->tester->seeRecord(Lead::class, [
			'id' => 2,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
		]);
		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => 1,
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'owner_id' => static::OWNER_ID,
		]);
		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => 2,
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
			'status_id' => LeadStatusInterface::STATUS_ARCHIVE,
			'owner_id' => static::OWNER_ID,
		]);
	}

	private function giveModel(int $status_id, array $ids): void {
		$this->model = new LeadStatusChangeForm([
			'ids' => $ids,
			'status_id' => $status_id,
			'owner_id' => static::OWNER_ID,
		]);
	}

	public function getModel(): LeadStatusChangeForm {
		return $this->model;
	}
}
