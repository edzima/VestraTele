<?php

namespace common\modules\lead\components;

use common\modules\lead\entities\Dialer;
use common\modules\lead\entities\DialerInterface;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadDialerType;
use common\modules\lead\models\searches\LeadDialerSearch;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class DialerManager extends Component {

	protected const LOG_CATEGORY = 'lead.dialer.manager';

	public const TYPE_EXTENSION = LeadDialerType::TYPE_EXTENSION;
	public const TYPE_QUEUE = LeadDialerType::TYPE_QUEUE;

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
			Yii::warning(static::logMessage('Dialer dont should call', $dialer), static::LOG_CATEGORY);
			$dialer->updateStatus($dialer->getStatusId());
			return false;
		}
		Yii::debug(static::logMessage('Calling Dialer', $dialer), static::LOG_CATEGORY);

		$dialer->updateStatus(Dialer::STATUS_CALLING);
		return true;
	}

	public function establish(DialerInterface $dialer): bool {
		if ($dialer->getStatusId() !== Dialer::STATUS_CALLING) {
			Yii::warning(static::logMessage('Only already called Dialer can be established.', $dialer), static::LOG_CATEGORY);
			return false;
		}
		Yii::debug(static::logMessage('Dialer establish.', $dialer), static::LOG_CATEGORY);
		$dialer->updateStatus(Dialer::STATUS_ESTABLISHED);
		return true;
	}

	public function notEstablish(DialerInterface $dialer): bool {
		if ($dialer->getStatusId() !== Dialer::STATUS_CALLING) {
			Yii::warning(static::logMessage('Only already called Dialer can be unestablished.', $dialer), static::LOG_CATEGORY);
			return false;
		}
		Yii::debug(static::logMessage('Dialer not establish.', $dialer), static::LOG_CATEGORY);

		$dialer->updateStatus(Dialer::STATUS_UNESTABLISHED);
		return true;
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
		$query = $this->getDataProvider()->query;
		foreach ($query->batch(10) as $rows) {
			foreach ($rows as $row) {
				/** @var LeadDialer $row */
				$dialer = $row->getDialer();
				if ($dialer->shouldCall()) {
					return $dialer;
				}
				if ($updateStatus) {
					Yii::debug(static::logMessage('Update Dialer status when find to call.', $dialer), static::LOG_CATEGORY);
					$dialer->updateStatus($dialer->getStatusId());
				}
			}
		}
		return null;
	}

	public function getDataProvider(): ActiveDataProvider {
		$searchModel = new LeadDialerSearch();
		$searchModel->onlyToCall = true;
		$searchModel->kindOfType = $this->type;
		$searchModel->typeUserId = $this->userId;
		return $searchModel->search();
	}

	protected static function logMessage(string $message, DialerInterface $dialer): array {
		return [
			'message' => $message,
			'id' => $dialer->getID(),
			'status' => $dialer->getStatusId(),
		];
	}
}
