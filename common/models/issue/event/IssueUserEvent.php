<?php

namespace common\models\issue\event;

use common\models\issue\IssueUser;
use yii\base\Event;

class IssueUserEvent extends Event {

	public const WILDCARD_EVENT = 'issueUserEvent.*';
	const EVENT_AFTER_LINK_USER_CREATE = 'issueUserEvent.afterLinkUserCreate';
	const EVENT_AFTER_LINK_USER_UPDATE = 'issueUserEvent.afterLinkUserUpdate';
	const EVENT_UNLINK_USER = 'issueUserEvent.unlinkUser';

	public IssueUser $issueUser;
}
