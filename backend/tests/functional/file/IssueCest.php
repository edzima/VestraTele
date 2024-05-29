<?php

namespace backend\tests\functional\file;

use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\FileFixtureHelper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\modules\file\controllers\IssueController;

class IssueCest {

	/** @see \backend\modules\issue\controllers\IssueController::actionUpload() */
	public const ROUTE_UPLOAD = '/file/issue/upload';

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			UserFixtureHelper::manager(),
			FileFixtureHelper::fixtures()
		);
	}

	public function checkAsManager(Manager $I): void {
		$I->amOnRoute(static::ROUTE_UPLOAD, ['id' => 1, 'type' => 1]);
	}
}
