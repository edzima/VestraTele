<?php

namespace common\components;

use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
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

	public function canSeeIssue(IssueInterface $model, bool $withChildes = true): bool {

		if ($this->can(Worker::ROLE_ADMINISTRATOR)) {
			return true;
		}

		if ($model->getIssueModel()->isArchived() && !$this->can(Worker::PERMISSION_ARCHIVE)) {
			Yii::warning('User: ' . $this->getId() . '  without permission try view archived issue.', 'issue.archived.' . $model->getIssueName());
			return false;
		}

		if ($this->can(Worker::ROLE_CUSTOMER_SERVICE) || $model->getIssueModel()->isForUser($this->getId())) {
			Yii::info('User: ' . $this->getId() . ' view issue.', 'issue.' . $model->getIssueName());
			return true;
		}
		if ($withChildes) {
			if ($this->can(Worker::ROLE_AGENT)) {
				$childesIds = Yii::$app->userHierarchy->getAllChildesIds($this->getId());
				if (!empty($childesIds)) {
					$forAgents = $model->getIssueModel()->isForAgents($childesIds);
					if ($forAgents) {
						Yii::info('Agent: ' . $this->getId() . ' view issue for self childes', 'issue.' . $model->getIssueName());
						return true;
					}
				}
			}
			Yii::warning('Agent: ' . $this->getId() . ' try view issue for not self childes.', 'issue.' . $model->getIssueName());
		}

		return false;
	}

	public function canDeleteNote(IssueNote $note): bool {
		if ($note->isSms()) {
			return $this->can(Worker::ROLE_ADMINISTRATOR);
		}
		return $note->user_id === $this->getId() || $this->can(Worker::PERMISSION_NOTE_DELETE);
	}
}
