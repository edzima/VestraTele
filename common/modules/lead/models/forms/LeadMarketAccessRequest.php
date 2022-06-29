<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use yii\base\Model;

class LeadMarketAccessRequest extends Model {

	public const DEFAULT_DAYS = 2;

	public int $user_id;
	public string $details = '';
	public int $days = self::DEFAULT_DAYS;

	private LeadMarket $market;
	private ?LeadMarketUser $model = null;

	public function rules() {
		return [
			[['!user_id', 'days'], 'required'],
		];
	}

//	public function __construct(LeadMarket $market, $config = []) {
//		if ($market->isDone() || $market->isArchived()) {
//			throw new InvalidConfigException('Market cannot be done or archived.');
//		}
//		$this->market = $market;
//		parent::__construct($config);
//	}

	public function getMarket(): LeadMarket {
		return $this->market;
	}

	public function getModel(): LeadMarketUser {
		if ($this->model === null) {
			$this->model = $this->market->leadMarketUsers[$this->user_id] ?? new LeadMarketUser();
		}
		return $this->model;
	}

	public function setModel(LeadMarketUser $leadMarketUser): void {
		$this->user_id = $leadMarketUser->user_id;
	}

}
