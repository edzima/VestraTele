<?php

namespace common\tests\unit\message;

use common\components\message\IssueSettlementMessageFactory;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssuePayInterface;
use common\models\issue\IssueUser;
use common\tests\unit\Unit;
use Yii;

class IssueSettlementMessageFactoryTest extends Unit {

	private IssueSettlementMessageFactory $factory;

	public function _before() {
		$this->factory = new IssueSettlementMessageFactory();
		parent::_before();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::types(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			MessageTemplateFixtureHelper::fixture(),
		);
	}

	public function testEmailFromName(): void {
		$message = $this->factory->createEmail();
		$this->assertContains(Yii::$app->name . ' Settlements', $message->getFrom());
	}

	public function testEmailAboutCreateHonorariumSettlementToCustomer(): void {
		$message = $this->factory->getEmailAboutCreateSettlementToCustomer(
			IssuePayCalculation::find()->onlyTypes([IssuePayCalculation::TYPE_HONORARIUM])->one()
		);
		$this->tester->assertNotEmpty($message);
		$this->tester->assertArrayHasKey($this->factory->getCustomerEmail(), $message->getTo());
		$this->tester->assertSame('Create Settlement Honorarium for Customer.', $message->getSubject());
		$this->tester->assertStringContainsString($this->factory->getAgentName(), $message->toString());
		$this->tester->assertStringContainsString($this->factory->getAgentPhone(), $message->toString());
		$this->tester->assertStringContainsString($this->factory->getAgentEmail(), $message->toString());
	}

	public function testSmsAboutCreateHonorariumSettlementToCustomer(): void {
		$settlement = IssuePayCalculation::find()->onlyTypes([IssuePayCalculation::TYPE_HONORARIUM])->one();
		$sms = $this->factory->getSmsAboutCreateSettlementToCustomer(
			$settlement
		);
		$this->tester->assertNotEmpty($sms);
		$this->tester->assertSame($settlement->getOwnerId(), $sms->owner_id);
		$this->tester->assertSame($this->factory->getCustomerPhone(), $sms->phone);
		$this->tester->assertSame('Sms About Create Settlement Honorarium for Customer.', $sms->note_title);
		$this->tester->assertStringContainsString(
			'New Settlement in Your Issue.',
			$sms->message
		);

		$this->tester->assertStringContainsString(
			$this->factory->getAgentName(),
			$sms->message
		);
		$this->tester->assertStringContainsString(
			$this->factory->getAgentPhone(),
			$sms->message
		);
		$this->tester->assertTrue($sms->validate());
	}

	public function testEmailAboutCreateHonorariumSettlementToWorkers(): void {
		$message = $this->factory->getEmailAboutCreateSettlementToWorkers(
			IssuePayCalculation::find()->onlyTypes([IssuePayCalculation::TYPE_HONORARIUM])->one(), [
				IssueUser::TYPE_AGENT,
				IssueUser::TYPE_TELEMARKETER,
			]
		);
		$this->tester->assertNotEmpty($message);
		$this->tester->assertArrayHasKey($this->factory->getAgentEmail(), $message->getTo());

		$this->tester->assertSame('Create Settlement Honorarium for Worker.', $message->getSubject());
		$this->tester->assertStringContainsString($this->factory->getCustomerName(), $message->toString());
		$this->tester->assertStringContainsString($this->factory->getCustomerPhone(), $message->toString());
		$this->tester->assertStringContainsString($this->factory->getCustomerEmail(), $message->toString());
	}

	public function testSmsAboutCreateHonorariumSettlementToAgent(): void {
		$settlement = IssuePayCalculation::find()->onlyTypes([IssuePayCalculation::TYPE_HONORARIUM])->one();
		$sms = $this->factory->getSmsAboutCreateSettlementToAgent(
			$settlement
		);
		$this->tester->assertNotEmpty($sms);
		$this->tester->assertSame($settlement->getOwnerId(), $sms->owner_id);
		$this->tester->assertSame($this->factory->getAgentPhone(), $sms->phone);
		$this->tester->assertSame('Sms About Create Settlement Honorarium for Agent.', $sms->note_title);
		$this->tester->assertStringContainsString(
			'New Settlement in Your Issue.',
			$sms->message
		);

		$this->tester->assertStringContainsString(
			$this->factory->getCustomerName(),
			$sms->message
		);
		$this->tester->assertStringContainsString(
			$this->factory->getCustomerPhone(),
			$sms->message
		);
		$this->tester->assertTrue($sms->validate());
	}

	public function testNotPayedPayEmailMessage(): void {
		$message = $this->factory->getEmailAboutPayedPayToCustomer(
			$this->tester->grabFixture(SettlementFixtureHelper::PAY, 'not-payed')
		);
		$this->tester->assertNull($message);
	}

	public function testPayedPayEmailMessageToCustomer(): void {
		/** @var IssuePayInterface $pay */
		$pay = $this->tester->grabFixture(SettlementFixtureHelper::PAY, 'payed');
		$message = $this->factory->getEmailAboutPayedPayToCustomer($pay);
		$this->tester->assertNotNull($message);
		$this->tester->assertArrayHasKey($this->factory->getCustomerEmail(), $message->getTo());
		$this->tester->assertSame('Email. Paid Pay for Customer.', $message->getSubject());
		$body = $message->getSwiftMessage()->getBody();
		$this->tester->assertStringContainsString(Yii::$app->formatter->asCurrency($pay->getValue()), $body);
		$this->tester->assertStringContainsString($this->factory->getAgentName(), $body);
		$this->tester->assertStringContainsString($this->factory->getAgentPhone(), $body);
		$this->tester->assertStringContainsString($this->factory->getAgentEmail(), $body);
	}
}
