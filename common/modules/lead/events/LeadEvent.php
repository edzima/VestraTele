<?php

namespace common\modules\lead\events;

use common\modules\lead\models\LeadInterface;
use yii\base\Event;

class LeadEvent extends Event {

	private LeadInterface $lead;

	public function __construct(LeadInterface $lead, $config = []) {
		$this->lead = $lead;
		parent::__construct($config);
	}

	public function getLead(): LeadInterface {
		return $this->lead;
	}
}
