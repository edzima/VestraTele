<?php

namespace common\components;

use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssueUser;
use common\models\user\UserVisible;
use common\models\user\Worker;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\User as BaseUser;

class User extends BaseUser {

	public function can($permissionName, $params = [], $allowCaching = true): bool {
		if (YII_ENV_TEST) {
			$allowCaching = false;
		}
		return parent::can($permissionName, $params, $allowCaching);
	}

	/**
	 * @param IssueInterface $model
	 * @param bool $withChildes
	 * @return bool
	 * @throws ForbiddenHttpException
	 */
	public function canSeeIssue(IssueInterface $model, bool $withChildes = true): bool {
		if ($this->can(Worker::ROLE_ADMINISTRATOR)) {
			return true;
		}

		$issue = $model->getIssueModel();
		if ($this->can(Worker::PERMISSION_ISSUE_VISIBLE_NOT_SELF)
			|| $issue->hasUser($this->getId())
			|| $issue->summonsHasUser($this->getId())
		) {
			return $this->canSeeArchivedIssue($model);
		}

		$excluded = UserVisible::hiddenUsers($this->getId());
		$issue = $model->getIssueModel();
		foreach ($excluded as $userId) {
			if ($issue->hasUser($userId)) {
				Yii::warning('User: ' . $this->getId() . ' try view issue for excluded user: ' . $userId . '.', 'issue.' . $model->getIssueName());
				return false;
			}
		}
		$included = UserVisible::visibleUsers($this->getId());
		foreach ($included as $userId) {
			if ($issue->hasUser($userId)) {
				return true;
			}
		}

		if ($withChildes) {
			if ($this->can(Worker::ROLE_AGENT)) {
				$childesIds = Yii::$app->userHierarchy->getAllChildesIds($this->getId());
				if (!empty($childesIds)) {
					foreach ($childesIds as $id) {
						if ($model->getIssueModel()->hasUser($id)) {
							Yii::info('Agent: ' . $this->getId() . ' view issue for self childes', 'issue.' . $model->getIssueName());
							return true;
						}
					}
				}
				Yii::warning('Agent: ' . $this->getId() . ' try view issue for not self childes.', 'issue.' . $model->getIssueName());
			}
		}

		return false;
	}

	protected function canSeeArchivedIssue(IssueInterface $model): bool {
		$issue = $model->getIssueModel();
		if (!$issue->isArchived()) {
			return true;
		}
		foreach (IssueUser::TYPES_ARCHIVE_ACCESS as $type) {
			if ($issue->hasUser(Yii::$app->user->getId(), $type)) {
				return true;
			}
		}
		return $this->can(Worker::PERMISSION_ARCHIVE) && $this->can(Worker::PERMISSION_ISSUE_VISIBLE_NOT_SELF);
	}

	public function canDeleteNote(IssueNote $note): bool {
		if ($note->isSms()) {
			return $this->can(Worker::ROLE_ADMINISTRATOR);
		}
		return $note->user_id === $this->getId() || $this->can(Worker::PERMISSION_NOTE_DELETE);
	}

	public function getFavoriteIssueType(): ?int {
		if ($this->isGuest || !($this->identity instanceof \common\models\user\User)) {
			return null;
		}
		return $this->identity->userProfile->favorite_issue_type_id;
	}
}
