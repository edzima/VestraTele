<?php

namespace common\tests\unit\message;

use common\components\message\IssueMessageFactory;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueSmsForm;
use common\models\issue\IssueUser;
use common\tests\unit\Unit;
use ymaker\email\templates\repositories\EmailTemplatesRepository;

class IssueMessageFactoryTest extends Unit {

	private IssueMessageFactory $factory;
	private MessageTemplateFixtureHelper $templateFixtureHelper;

	public function _before() {
		parent::_before();
		$this->factory = new IssueMessageFactory();
		$this->factory->issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$this->templateFixtureHelper = new MessageTemplateFixtureHelper($this->tester);
		$this->templateFixtureHelper->setRepository(new EmailTemplatesRepository());
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(true),
			MessageTemplateFixtureHelper::fixture(),
		);
	}

	public function testCreateWithoutParam(): void {
		$sms = $this->factory->createSms();
		$this->tester->assertInstanceOf(IssueSmsForm::class, $sms);
		$this->tester->assertEmpty($sms->phone);
		$this->tester->assertEmpty($sms->message);
		$this->tester->assertSame($sms->userTypes, array_keys(IssueUser::getTypesNames()));
	}

	public function testCreateWithParams(): void {
		$sms = $this->factory->createSms([
			'phone' => '123123123',
			'message' => 'Test Message',
			'userTypes' => [IssueUser::TYPE_CUSTOMER],
		]);
		$this->tester->assertSame('123123123', $sms->phone);
		$this->tester->assertSame('Test Message', $sms->message);
		$this->tester->assertContains(IssueUser::TYPE_CUSTOMER, $sms->userTypes);
	}

	public function testGenerateKeyWithoutIssueTypesIds(): void {
		$key = IssueMessageFactory::generateKey('sms', 'issue.create.customer');
		$this->tester->assertSame('sms.issue.create.customer', $key);
	}

	public function testAboutCreateToCustomer(): void {
		$sms = $this->factory->getSmsAboutCreateIssueToCustomer(1);
		$this->tester->assertSame(1, $sms->owner_id);
		$this->tester->assertCount(1, $sms->userTypes);
		$this->tester->assertContains(IssueUser::TYPE_CUSTOMER, $sms->userTypes);
		$this->tester->assertNotEmpty($sms->phone);
		$this->tester->assertNotEmpty($sms->message);
		$this->tester->assertNotEmpty($sms->note_title);
		$this->tester->assertSame('Sms About Create Issue for Customer.', $sms->note_title);
		$this->tester->assertSame($this->factory->getCustomerPhone(), $sms->phone);
		$this->tester->assertStringContainsString($this->factory->getAgentPhone(), $sms->message);
		$this->tester->assertStringContainsString($this->factory->getAgentName(), $sms->message);
		$this->tester->assertSame($this->factory->getCustomerPhone(), $sms->phone);
		$this->tester->assertTrue($sms->validate());
	}

	public function testAboutCreateToCustomerToNotExistedIssueTypeTemplate(): void {
		$this->factory->issue = Issue::find()->andWhere(['type_id' => 3])->one();
		$sms = $this->factory->getSmsAboutCreateIssueToCustomer(1);
		$this->tester->assertNull($sms);
		$this->templateFixtureHelper->save(
			IssueMessageFactory::generateKey(IssueMessageFactory::TYPE_SMS, IssueMessageFactory::keyAboutCreateIssueToCustomer(), [3]),
			'Note Title for type 3',
			'Message for Type 3.'
		);
		$sms = $this->factory->getSmsAboutCreateIssueToCustomer(1);
		$this->tester->assertNotNull($sms);
		$this->tester->assertSame('Note Title for type 3', $sms->note_title);
		$this->tester->assertSame('Message for Type 3.', $sms->message);
		$this->tester->assertTrue($sms->validate());
	}

	public function testAboutCreateToAgent(): void {
		$message = $this->factory->getEmailAboutCreateIssueToAgent();
		$this->tester->assertNotNull($message);
		$this->tester->assertNotEmpty($message->getSubject());
		$this->tester->assertNotEmpty($message->toString());
		$this->tester->assertSame('Email. New Issue for Worker.', $message->getSubject());
		$this->tester->assertStringContainsString($this->factory->getCustomerName(), $message->toString());
		$this->tester->assertStringContainsString($this->factory->getCustomerPhone(), $message->toString());
	}

}
