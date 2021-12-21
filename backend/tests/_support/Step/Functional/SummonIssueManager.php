<?php

namespace backend\tests\Step\Functional;

use common\models\user\Worker;

/**
 * Class SummonIssueManager
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonIssueManager extends IssueManager {

	protected function getUsername(): string {
		return 'summon-issue-manager';
	}

	protected function getPermissions(): array {
		//@todo create role for Summon Manager.
		return array_merge(parent::getPermissions(), [Worker::PERMISSION_SUMMON_MANAGER, Worker::PERMISSION_SUMMON, Worker::PERMISSION_SUMMON_CREATE]);
	}
}
