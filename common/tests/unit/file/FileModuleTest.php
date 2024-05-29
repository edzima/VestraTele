<?php

namespace common\tests\unit\file;

use common\fixtures\helpers\FileFixtureHelper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\modules\file\models\File;
use common\modules\file\models\IssueFile;
use common\modules\file\Module;
use common\tests\unit\Unit;
use Yii;
use yii\base\Exception;

class FileModuleTest extends Unit {

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			FileFixtureHelper::fixtures(),
			['agent' => UserFixtureHelper::agent()],
		);
	}

	public function testAttachFile(): void {
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		/** @var Module $module */
		$module = Yii::$app->getModule('file');
		$baseFilePath = Yii::getAlias('@common/tests/_data/file/file.png');
		$filePath = Yii::getAlias('@common/tests/_data/file/test-file.png');
		copy($baseFilePath, $filePath);
		$file = $module->attachFile(
			$filePath,
			$issue,
			1,
			UserFixtureHelper::AGENT_AGNES_MILLER
		);
		$this->tester->assertInstanceOf(File::class, $file);
		$this->tester->seeRecord(
			IssueFile::class, [
				'issue_id' => $issue->id,
				'file_id' => $file->id,
			]
		);
		$this->tester->assertFalse(file_exists($filePath));
	}

	public function testNotExistedFile(): void {

		$filePath = Yii::getAlias('@common/tests/_data/file/file-not-exist.png');
		$this->tester->expectThrowable(new Exception(
			"File '$filePath' not exists."
		), function () use ($filePath) {
			$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
			/** @var Module $module */
			$module = Yii::$app->getModule('file');
			$module->attachFile(
				$filePath,
				$issue,
				1);
		});
	}
}
