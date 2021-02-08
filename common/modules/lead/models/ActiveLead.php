<?php

namespace common\modules\lead\models;

use yii\db\ActiveRecordInterface;

interface ActiveLead extends LeadInterface, ActiveRecordInterface {

	public static function createFromLead(LeadInterface $lead): self;

	public static function findByLead(LeadInterface $lead): ?self;
}
