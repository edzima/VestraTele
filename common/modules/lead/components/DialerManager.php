<?php

namespace common\modules\lead\components;

use common\modules\lead\entities\Dialer;
use common\modules\lead\entities\DialerInterface;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\query\LeadDialerQuery;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class DialerManager extends Component {

	public $userId;
	public ?int $type = null;

	public function init() {
		parent::init();
		if ($this->userId === null) {
			$this->userId = Yii::$app->user->getId();
		}
		if ($this->userId === null) {
			throw new InvalidConfigException('$userId must be set or User must be logged.');
		}
	}

	public function calling(DialerInterface $dialer): bool {
		if (!$dialer->shouldCall()) {
			$dialer->updateStatus($dialer->getStatusId());
			return false;
		}
		$dialer->updateStatus(Dialer::STATUS_CALLING);
		return true;
	}

	public function establish(DialerInterface $dialer): bool {
		if ($dialer->getStatusId() !== Dialer::STATUS_ESTABLISH) {
			$dialer->updateStatus(Dialer::STATUS_ESTABLISH);
			return true;
		}
		return false;
	}

	public function notEstablish(DialerInterface $dialer): bool {
		if ($dialer->getStatusId() !== Dialer::STATUS_NOT_ESTABLISH) {
			$dialer->updateStatus(Dialer::STATUS_NOT_ESTABLISH);
			return true;
		}
		return false;
	}

	public function find(int $id): ?DialerInterface {
		$model = LeadDialer::find()
			->userType($this->userId)
			->andWhere([LeadDialer::tableName() . '.id' => $id])
			->one();
		if ($model) {
			return $model->getDialer();
		}
		return null;
	}

	public function findToCall(bool $updateStatus = true): ?DialerInterface {
		$query = $this->toCallQuery();
		foreach ($query->batch(10) as $rows) {
			foreach ($rows as $row) {
				/** @var LeadDialer $row */
				$dialer = $row->getDialer();
				if ($dialer->shouldCall()) {
					return $dialer;
				}
				if ($updateStatus) {
					$dialer->updateStatus($dialer->getStatusId());
				}
			}
		}
		return null;
	}

	public function toCallQuery(): LeadDialerQuery {
		$query = LeadDialer::find()
			->activeType()
			->toCall()
			->userType($this->userId)
			->joinWith('lead')
			->joinWith('lead.reports');
		if ($this->type) {
			$query->type($this->type);
		}
		return $query;
	}
}
