<?php

namespace common\modules\lead\events;

use common\modules\lead\components\LeadDialerManager;
use common\modules\lead\models\ActiveLead;
use yii\base\Event;

/**
 * @property $sender LeadDialer
 */
class LeadDialerEvent extends Event {

	/**
	 * @var LeadDialerManager
	 * @inheritdoc
	 */
	public $sender;

	private ActiveLead $lead;
	public bool $reported = false;

	public function __construct(ActiveLead $lead, $config = []) {
		$this->lead = $lead;
		parent::__construct($config);
	}

	public function getLead(): ActiveLead {
		return $this->lead;
	}

}
