<?php

namespace common\modules\lead\models;

use yii\db\ActiveRecordInterface;

interface ActiveLead extends LeadInterface, ActiveRecordInterface {

	public function getId(): int;

	public function updateStatus(int $status_id): bool;

	public static function findById(int $id): ?self;

	public static function createFromLead(LeadInterface $lead): self;

	public static function findByLead(LeadInterface $lead): ?self;
}
