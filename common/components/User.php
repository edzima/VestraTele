<?php

namespace common\components;

use common\models\issue\Issue;
use common\models\user\Worker;
use Yii;
use yii\web\User as BaseUser;

class User extends BaseUser {

	public function can($permissionName, $params = [], $allowCaching = true): bool {
		if (YII_ENV_TEST) {
			$allowCaching = false;
		}
		return parent::can($permissionName, $params, $allowCaching);
	}

	public function canSeeIssue(Issue $model, bool $withChildes = true): bool {

		if ($this->can(Worker::ROLE_ADMINISTRATOR)) {
			return true;
		}

		if ($model->isArchived() && !$this->can(Worker::PERMISSION_ARCHIVE)) {
			Yii::warning('User: ' . $this->getId() . '  without permission try view archived issue.', 'issue.archived.' . $model->longId);
			return false;
		}

		if ($this->can(Worker::ROLE_CUSTOMER_SERVICE) || $model->isForUser($this->getId())) {
			Yii::info('User: ' . $this->getId() . ' view issue.', 'issue.' . $model->longId);
			return true;
		}
		if ($withChildes) {
			if ($this->can(Worker::ROLE_AGENT)) {
				$childesIds = Yii::$app->userHierarchy->getAllChildesIds($this->getId());
				if (!empty($childesIds)) {
					$forAgents = $model->isForAgents($childesIds);
					if ($forAgents) {
						Yii::info('Agent: ' . $this->getId() . ' view issue for self childes', 'issue.' . $model->longId);
						return true;
					}
				}
			}
			Yii::warning('Agent: ' . $this->getId() . ' try view issue for not self childes.', 'issue.' . $model->longId);
		}

		return false;
	}
}
