<?php

namespace common\tests\unit\sms;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueNote;
use common\models\issue\IssueSmsForm;
use common\models\issue\IssueUser;
use console\jobs\IssueSmsSendJob;

/**
 * @property IssueSmsForm $model
 */
class IssueSmsFormTest extends SmsFormTest {

	private const DEFAULT_ISSUE_ID = 1;
	private const DEFAULT_OWNER_ID = UserFixtureHelper::AGENT_PETER_NOWAK;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::customer(true),
			IssueFixtureHelper::agent(true),
			IssueFixtureHelper::note()
		);
	}

	public function beforePushJob(): void {
		$this->model->scenario = IssueSmsForm::SCENARIO_SINGLE;
		$this->model->phone = array_key_first($this->model->getPhones());
		parent::beforePushJob();
	}

	public function testNotIssueUserPhone(): void {
		$this->giveModel([
			'scenario' => IssueSmsForm::SCENARIO_SINGLE,
			'phone' => '+48 682 222 110',
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Phone number is invalid.', 'phone');

		$this->giveModel([
			'scenario' => IssueSmsForm::SCENARIO_MULTIPLE,
			'phones' => ['+48 682 222 110'],
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Phones numbers is invalid.', 'phones');
	}

	public function testCustomerPhone(): void {
		$this->giveModel([
			'scenario' => IssueSmsForm::SCENARIO_SINGLE,
			'phone' => '+48 673 222 110',
		]);
		$this->thenSuccessValidate(['phone']);

		$this->giveModel([
			'scenario' => IssueSmsForm::SCENARIO_MULTIPLE,
			'phones' => ['+48 673 222 110'],
		]);
		$this->thenSuccessValidate(['phones']);

		$this->giveModel([
			'scenario' => IssueSmsForm::SCENARIO_SINGLE,
			'phone' => '+48 673 222 110',
			'userTypes' => [IssueUser::TYPE_AGENT],
		]);
		$this->thenUnsuccessValidate(['phone']);
		$this->thenSeeError('Phone number is invalid.', 'phone');

		$this->giveModel([
			'scenario' => IssueSmsForm::SCENARIO_MULTIPLE,
			'phones' => ['+48 673 222 110'],
			'userTypes' => [IssueUser::TYPE_AGENT],
		]);
		$this->thenUnsuccessValidate(['phones']);
		$this->thenSeeError('Phones numbers is invalid.', 'phones');
	}

	public function testNoteWithDefaultTitle(): void {
		$this->giveModel([
			'phone' => '48123123123',
			'message' => 'Test Message',
		]);
		$this->tester->assertTrue($this->model->note('TEST_ID'));
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => static::DEFAULT_ISSUE_ID,
			'title' => 'SMS Sent',
			'description' => 'Test Message',
			'user_id' => static::DEFAULT_OWNER_ID,
			'type' => IssueNote::generateType(IssueNote::TYPE_SMS, 'TEST_ID'),
		]);
	}

	public function testNoteWithCustomTitle(): void {
		$this->giveModel([
			'phone' => '48123123123',
			'message' => 'Test Message',
			'note_title' => 'Custom Title',
		]);
		$this->tester->assertTrue($this->model->note('TEST_ID'));
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => static::DEFAULT_ISSUE_ID,
			'title' => 'Custom Title',
			'description' => 'Test Message',
			'user_id' => static::DEFAULT_OWNER_ID,
			'type' => IssueNote::generateType(IssueNote::TYPE_SMS, 'TEST_ID'),
		]);
	}

	protected function jobClass(): string {
		return IssueSmsSendJob::class;
	}

	protected function giveModel(array $config = [], int $issue_id = self::DEFAULT_ISSUE_ID): void {
		if (!isset($config['owner_id'])) {
			$config['owner_id'] = static::DEFAULT_OWNER_ID;
		}
		if (!isset($config['userTypes'])) {
			$config['userTypes'] = [IssueUser::TYPE_CUSTOMER, IssueUser::TYPE_AGENT];
		}
		$this->model = new IssueSmsForm($issue_id, $config);
	}
}
