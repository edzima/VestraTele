<?php

namespace backend\tests\issue;

use backend\modules\issue\models\IssueForm;
use backend\modules\issue\models\IssueStage;
use backend\tests\UnitTester;
use common\fixtures\issue\EntityResponsibleFixture;
use common\fixtures\issue\IssueFixture;
use common\fixtures\issue\IssueUserFixture;
use common\fixtures\issue\StageFixture;
use common\fixtures\issue\StageTypesFixtures;
use common\fixtures\issue\TypeFixture;
use common\fixtures\user\AgentFixture;
use common\fixtures\user\CustomerFixture;
use common\fixtures\user\LawyerFixture;
use common\fixtures\user\TelemarketerFixture;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\User;
use Yii;
use yii\base\InvalidConfigException;

class IssueFormTest extends \Codeception\Test\Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	protected function _before() {
		$this->tester->haveFixtures([
			'issue' => [
				'class' => IssueFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/issue.php',
			],
			'user' => [
				'class' => IssueUserFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/users.php',

			],
			'customer' => [
				'class' => CustomerFixture::class,
				'dataFile' => codecept_data_dir() . 'customer.php',
			],
			'agent' => [
				'class' => AgentFixture::class,
				'dataFile' => codecept_data_dir() . 'agent.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'lawyer' => [
				'class' => LawyerFixture::class,
				'dataFile' => codecept_data_dir() . 'lawyer.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'telemarketer' => [
				'class' => TelemarketerFixture::class,
				'dataFile' => codecept_data_dir() . 'telemarketer.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'stage' => [
				'class' => StageFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/stage.php',
			],
			'type' => [
				'class' => TypeFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/type.php',
			],
			'stage-types' => [
				'class' => StageTypesFixtures::class,
				'dataFile' => codecept_data_dir() . 'issue/stage_types.php',
			],
			'entity' => [
				'class' => EntityResponsibleFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/entity_responsible.php',
			],
		]);
	}

	public function testWorkersList() {
		return;
		$this->tester->assertCount(2, IssueForm::getAgents());
		$agent = $this->tester->grabFixture('agent', 0);
		Yii::$app->authManager->revoke(Yii::$app->authManager->getPermission(User::PERMISSION_ISSUE), $agent->id);
		$this->tester->assertCount(1, IssueForm::getAgents());

		$this->tester->assertCount(2, IssueForm::getLawyers());
		$lawyer = $this->tester->grabFixture('lawyer', 0);
		Yii::$app->authManager->revoke(Yii::$app->authManager->getPermission(User::PERMISSION_ISSUE), $lawyer->id);
		$this->tester->assertCount(1, IssueForm::getLawyers());

		$this->tester->assertCount(2, IssueForm::getTele());
		$tele = $this->tester->grabFixture('telemarketer', 0);
		Yii::$app->authManager->revoke(Yii::$app->authManager->getPermission(User::PERMISSION_ISSUE), $tele->id);
		$this->tester->assertCount(1, IssueForm::getTele());
	}

	public function testCreateWithoutCustomerOrModel() {
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			new IssueForm();
		});
	}

	public function testCorrectCreate(): void {
		$model = $this->createModel([
			'type_id' => 1,
			'stage_id' => 1,
			'entity_responsible_id' => 1,
		]);
		$this->tester->assertTrue($model->save());
	}

	public function testInvalidStageType(): void {
		$model = $this->createModel([
			'type_id' => 3,
			'stage_id' => 1,
			'entity_responsible_id' => 1,
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Stage is invalid.', $model->getFirstError('stage_id'));
	}

	public function testArchiveEmpty(): void {
		$model = $this->createModel([
			'type_id' => 1,
			'stage_id' => IssueStage::ARCHIVES_ID,
			'entity_responsible_id' => 1,
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Archives cannot be blank.', $model->getFirstError('archives_nr'));
	}

	public function testValidArchive(): void {
		$model = $this->createModel([
			'type_id' => 1,
			'stage_id' => IssueStage::ARCHIVES_ID,
			'entity_responsible_id' => 1,
			'archives_nr' => 'A1222',
		]);
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(Issue::class, [
			'archives_nr' => 'A1222',
		]);
	}

	public function testChangeStateWithoutDate(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture('issue', 0);
		$this->tester->assertNull($issue->stage_change_at);
		$model = new IssueForm(['model' => $issue]);
		$model->stage_id = 1;
		$this->tester->assertTrue($model->save());
		$this->tester->assertSame(date('Y-m-d'), date('Y-m-d', strtotime($model->getModel()->stage_change_at)));
	}

	public function testChangeStateWithDate(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture('issue', 0);
		$this->tester->assertNull($issue->stage_change_at);
		$model = new IssueForm(['model' => $issue]);
		$model->stage_id = 1;
		$model->stage_change_at = '2020-10-10';
		$this->tester->assertTrue($model->save());
		$this->tester->assertSame('2020-10-10', $model->getModel()->stage_change_at);
	}

	public function testUnlinkTele(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture('issue', 0);
		$this->tester->assertNotNull($issue->tele);
		$teleId = $issue->tele->id;
		$model = new IssueForm(['model' => $issue]);
		$model->tele_id = null;
		$this->tester->assertTrue($model->save());
		$issue = $model->getModel();
		$issue->refresh();
		$this->tester->assertNull($issue->tele);
		$this->tester->dontSeeRecord(IssueUser::class, ['issue_id' => $issue->id, 'user_id' => $teleId, 'type' => IssueUser::TYPE_TELEMARKETER]);
	}

	public function testNotUniqueArchive(): void {
		$model = $this->createModel([
			'type_id' => 1,
			'stage_id' => IssueStage::ARCHIVES_ID,
			'entity_responsible_id' => 1,
			'archives_nr' => 'A1222',
		]);
		$this->tester->assertTrue($model->save());
		$model = $this->createModel([
			'type_id' => 1,
			'stage_id' => IssueStage::ARCHIVES_ID,
			'entity_responsible_id' => 1,
			'archives_nr' => 'A1222',
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Archives "A1222" has already been taken.', $model->getFirstError('archives_nr'));
	}

	private function createModel(array $attributes = []): IssueForm {
		if (!isset($attributes['customer'])) {
			$attributes['customer'] = $this->tester->grabFixture('customer', 0);
		}
		if (!isset($attributes['lawyer_id'])) {
			$attributes['lawyer_id'] = 200;
		}
		if (!isset($attributes['agent_id'])) {
			$attributes['agent_id'] = 300;
		}

		return new IssueForm($attributes);
	}
}
