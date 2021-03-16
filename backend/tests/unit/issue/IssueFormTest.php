<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueForm;
use backend\modules\issue\models\IssueStage;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\User;
use Yii;
use yii\base\InvalidConfigException;

class IssueFormTest extends Unit {

	private IssueFixtureHelper $issueFixture;

	protected function _before(): void {
		parent::_before();
		$this->issueFixture = new IssueFixtureHelper($this->tester);
		codecept_debug(array_keys($this->tester->grabFixtures()));
	}

	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures();
	}

	public function testWorkersList(): void {
		$activeAgentsCount = 3;

		$this->tester->assertCount($activeAgentsCount, IssueForm::getAgents());
		$agent = $this->tester->grabFixture(IssueFixtureHelper::AGENT, 'some-agent');
		Yii::$app->authManager->revoke(Yii::$app->authManager->getPermission(User::PERMISSION_ISSUE), $agent->id);
		$this->tester->assertCount($activeAgentsCount - 1, IssueForm::getAgents());

		$this->tester->assertCount(2, IssueForm::getLawyers());
		$lawyer = $this->tester->grabFixture(IssueFixtureHelper::LAWYER, 0);
		Yii::$app->authManager->revoke(Yii::$app->authManager->getPermission(User::PERMISSION_ISSUE), $lawyer->id);
		$this->tester->assertCount(1, IssueForm::getLawyers());

		$this->tester->assertCount(2, IssueForm::getTele());
		$tele = $this->tester->grabFixture(IssueFixtureHelper::TELEMARKETER, 0);
		Yii::$app->authManager->revoke(Yii::$app->authManager->getPermission(User::PERMISSION_ISSUE), $tele->id);
		$this->tester->assertCount(1, IssueForm::getTele());
	}

	public function testCreateWithoutCustomerOrModel(): void {
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			new IssueForm();
		});
	}

	public function testCorrectCreate(): void {
		$model = $this->createModel([
			'details' => 'Test details',
			'signature_act' => 'I OC 20/20',
		]);
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(Issue::class, [
			'details' => 'Test details',
			'signature_act' => 'I OC 20/20',
		]);
	}

	public function testInvalidStageType(): void {
		$model = $this->createModel([
			'type_id' => 3,
			'stage_id' => 1,
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Stage is invalid.', $model->getFirstError('stage_id'));
	}

	public function testArchiveEmpty(): void {
		$model = $this->createModel([
			'stage_id' => IssueStage::ARCHIVES_ID,
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Archives cannot be blank.', $model->getFirstError('archives_nr'));
	}

	public function testValidArchive(): void {
		$model = $this->createModel([
			'stage_id' => IssueStage::ARCHIVES_ID,
			'archives_nr' => 'A1222',
		]);
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(Issue::class, [
			'archives_nr' => 'A1222',
		]);
	}

	public function testCheckStateAtWithoutChangeStage(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$this->tester->assertNull($issue->stage_change_at);
		$model = new IssueForm(['model' => $issue]);
		$model->save();
		$this->tester->assertTrue($model->save());
		$this->tester->assertSame(date('Y-m-d'), date('Y-m-d', strtotime($model->getModel()->stage_change_at)));
	}

	public function testChangeStateWithDate(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$this->tester->assertNull($issue->stage_change_at);
		$model = new IssueForm(['model' => $issue]);
		$model->stage_id = 1;
		$model->stage_change_at = '2020-10-10';
		$this->tester->assertTrue($model->save());
		$this->tester->assertSame('2020-10-10', $model->getModel()->stage_change_at);
	}

	public function testUnlinkTele(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
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

	public function testNotUniqueSignatureAct(): void {
		$model = $this->createModel([
			'signature_act' => 'I OC 22',
		]);
		$this->tester->assertTrue($model->save());
		$model = $this->createModel([
			'signature_act' => 'I OC 22',
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Signature act "I OC 22" has already been taken.', $model->getFirstError('signature_act'));
	}

	private function createModel(array $attributes = []): IssueForm {
		if (!isset($attributes['customer'])) {
			$attributes['customer'] = $this->tester->grabFixture(IssueFixtureHelper::CUSTOMER, 0);
		}
		if (!isset($attributes['lawyer_id'])) {
			$attributes['lawyer_id'] = 200;
		}
		if (!isset($attributes['agent_id'])) {
			$attributes['agent_id'] = 300;
		}

		if (!isset($attributes['type_id'])) {
			$attributes['type_id'] = 1;
		}
		if (!isset($attributes['stage_id'])) {
			$attributes['stage_id'] = 1;
		}

		if (!isset($attributes['entity_responsible_id'])) {
			$attributes['entity_responsible_id'] = 1;
		}

		if (!isset($attributes['signing_at'])) {
			$attributes['signing_at'] = date('Y-m-d');
		}

		return new IssueForm($attributes);
	}
}
