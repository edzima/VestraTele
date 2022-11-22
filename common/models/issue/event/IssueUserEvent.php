<?php

namespace common\models\issue\event;

use common\models\issue\IssueUser;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;

class IssueUserEvent extends Event {

	public const WILDCARD_EVENT = 'issueUserEvent.*';
	const EVENT_AFTER_LINK_USER_CREATE = 'issueUserEvent.afterLinkUserCreate';
	const EVENT_AFTER_LINK_USER_UPDATE = 'issueUserEvent.afterLinkUserUpdate';
	const EVENT_UNLINK_USER = 'issueUserEvent.unlinkUser';

	public IssueUser $model;

	public function getTranslateName(): string {
		switch ($this->name) {
			case static::EVENT_AFTER_LINK_USER_CREATE:
				return Yii::t('issue', 'Add {userWithType} to Issue: {issue}.', [
					'userWithType' => $this->model->getTypeWithUser(),
					'issue' => $this->model->getIssueName(),
				]);
			case static::EVENT_AFTER_LINK_USER_UPDATE:
				return Yii::t('issue', 'Update {userWithType} to Issue: {issue}.', [
					'userWithType' => $this->model->getTypeWithUser(),
					'issue' => $this->model->getIssueName(),
				]);
			case static::EVENT_UNLINK_USER:
				return Yii::t('issue', 'Delete {userWithType} from Issue: {issue}.', [
					'userWithType' => $this->model->getTypeWithUser(),
					'issue' => $this->model->getIssueName(),
				]);
		}
		throw new InvalidConfigException('Invalid $name');
	}
}
