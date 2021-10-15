<?php

namespace common\tests\unit\sms;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssueSmsForm;
use common\models\issue\IssueUser;
use console\jobs\IssueSmsSendJob;

/**
 * @property IssueSmsForm $model
 */
class IssueSmsFormTest extends SmsFormTest {

	private const DEFAULT_ISSUE_FIXTURE_INDEX = 0;
	private const DEFAULT_OWNER_ID = UserFixtureHelper::AGENT_PETER_NOWAK;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::note()
		);
	}

	public function beforePushJob(): void {
		$this->model->scenario = IssueSmsForm::SCENARIO_SINGLE;
		$this->model->phone = array_key_first($this->model->getPhones());
		parent::beforePushJob();
	}

	public function testFirstAvailableNumber(): void {
		$this->giveModel(['userTypes' => [IssueUser::TYPE_CUSTOMER]]);
		$this->model->setFirstAvailablePhone();
		$this->tester->assertSame('48673222110', $this->model->phone);
		$this->tester->assertEmpty($this->model->phones);
		$this->model->scenario = IssueSmsForm::SCENARIO_MULTIPLE;
		$this->model->setFirstAvailablePhone();
		$this->tester->assertContains('48673222110', $this->model->phones);

		$this->giveModel(['userTypes' => [IssueUser::TYPE_AGENT]]);
		$this->model->setFirstAvailablePhone();
		$this->tester->assertSame('48122222300', $this->model->phone);
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

	public function testNotSelfPhone(): void {
		$this->giveModel([
			'allowSelf' => false,
			'owner_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
			'userTypes' => [
				IssueUser::TYPE_CUSTOMER,
			],
		]);
		$this->tester->assertEmpty($this->model->getPhones());

		$this->giveModel([
			'allowSelf' => true,
			'owner_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
			'userTypes' => [
				IssueUser::TYPE_CUSTOMER,
			],
		]);
		$this->tester->assertNotEmpty($this->model->getPhones());
		$this->tester->assertContains('48673222110', $this->model->getPhones());

		$this->giveModel([
			'allowSelf' => false,
			'owner_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
			'userTypes' => [
				IssueUser::TYPE_CUSTOMER,
				IssueUser::TYPE_AGENT,
			],
		]);
		$this->tester->assertNotEmpty($this->model->getPhones());
		$this->tester->assertNotContains('48673222110', $this->model->getPhones());
		$this->tester->assertContains('48122222300', $this->model->getPhones());
	}

	public function testPhoneIssueUserName(): void {
		$this->giveModel(['userTypes' => [IssueUser::TYPE_CUSTOMER]]);
		$this->tester->assertNull($this->model->getPhoneIssueUserName());
		$this->model->setFirstAvailablePhone();
		$this->tester->assertSame(
			'client: Wayne John[+48 673 222 110]',
			$this->model->getPhoneIssueUserName()
		);
		$this->tester->assertSame(
			'client: Wayne John[+48 673 222 110]',
			$this->model->getPhoneIssueUserName(null, IssueUser::TYPE_CUSTOMER)
		);
		$this->tester->assertSame(
			'client: Wayne John[+48 673 222 110]',
			$this->model->getPhoneIssueUserName('48 673 222 110')
		);
		$this->tester->assertSame(
			'client: Wayne John[+48 673 222 110]',
			$this->model->getPhoneIssueUserName('48 673 222 110', IssueUser::TYPE_CUSTOMER)
		);
		$this->tester->assertSame(
			'client: Wayne John[+48 673 222 220]',
			$this->model->getPhoneIssueUserName('48673222220')
		);
		$this->tester->assertNull($this->model->getPhoneIssueUserName(null, IssueUser::TYPE_AGENT));

		$this->giveModel(['userTypes' => [IssueUser::TYPE_AGENT]]);
		$this->tester->assertNull($this->model->getPhoneIssueUserName());
		$this->model->setFirstAvailablePhone();
		$this->tester->assertSame('agent: Nowak Peter[+48 122 222 300]', $this->model->getPhoneIssueUserName());
		$this->tester->assertSame('agent: Nowak Peter[+48 122 222 300]', $this->model->getPhoneIssueUserName('+48 122 222 300'));

		$this->giveModel(['userTypes' => [IssueUser::TYPE_CUSTOMER, IssueUser::TYPE_AGENT]]);
		$this->tester->assertNull($this->model->getPhoneIssueUserName());
		$this->tester->assertSame(
			'client: Wayne John[+48 673 222 110]',
			$this->model->getPhoneIssueUserName('48 673 222 110', IssueUser::TYPE_CUSTOMER)
		);
		$this->tester->assertSame('agent: Nowak Peter[+48 122 222 300]', $this->model->getPhoneIssueUserName('+48 122 222 300'));
	}

	public function testNoteWithDefaultTitle(): void {
		$this->giveModel([
			'message' => 'Test Message',
		]);
		$this->model->setFirstAvailablePhone();
		$this->tester->assertTrue($this->model->note('TEST_ID'));
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => $this->model->getIssue()->getIssueId(),
			'title' => 'SMS Sent - ' . $this->model->getPhoneIssueUserName(),
			'description' => 'Test Message',
			'user_id' => static::DEFAULT_OWNER_ID,
			'type' => IssueNote::genereateSmsType($this->model->phone, 'TEST_ID'),
		]);
	}

	public function testNoteWithCustomTitle(): void {
		$this->giveModel([
			'message' => 'Test Message',
			'note_title' => 'Custom Title',
		]);
		$this->model->setFirstAvailablePhone();
		$this->tester->assertTrue($this->model->note('TEST_ID'));
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => $this->model->getIssue()->getIssueId(),
			'title' => 'Custom Title - ' . $this->model->getPhoneIssueUserName(),
			'description' => 'Test Message',
			'user_id' => static::DEFAULT_OWNER_ID,
			'type' => IssueNote::genereateSmsType($this->model->phone, 'TEST_ID'),
		]);
	}

	protected function giveModel(array $config = [], IssueInterface $issue = null): void {
		if ($issue === null) {
			$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, static::DEFAULT_ISSUE_FIXTURE_INDEX);
		}
		if (!isset($config['owner_id'])) {
			$config['owner_id'] = static::DEFAULT_OWNER_ID;
		}
		if (!isset($config['userTypes'])) {
			$config['userTypes'] = [IssueUser::TYPE_CUSTOMER, IssueUser::TYPE_AGENT];
		}
		if (!isset($config['allowSelf'])) {
			$config['allowSelf'] = true;
		}
		$this->model = new IssueSmsForm($issue, $config);
	}

	protected function jobClass(): string {
		return IssueSmsSendJob::class;
	}
}
