<?php

namespace common\tests\unit;

use Swift_TransportException;
use Yii;
use yii\swiftmailer\Mailer;

class MailTest extends Unit {

	private Mailer $realMailer;
	private array $realTransportSettings;

	public function _before():void {
		parent::_before();
		$this->realTransportSettings = [
			'class' => 'Swift_SmtpTransport',
			'encryption' => getenv('EMAIL_ENCRYPTION'),
			'host' => getenv('EMAIL_SMTP_HOST'),
			'port' => getenv('EMAIL_SMTP_PORT'),
			'username' => getenv('EMAIL_USERNAME'),
			'password' => getenv('EMAIL_PASSWORD'),
			'timeout' => 5 //sec
		];

		$this->realMailer = new Mailer([
			'viewPath' => '@common/mail',
			'useFileTransport' => false,
			'transport' => $this->realTransportSettings,
		]);
	}

	public function testSendBasicEmail(): void {
		$emailTo = 'example@mail.com';

		Yii::$app->mailer->compose()
			->setFrom('foo@bar.com')
			->setTo($emailTo)
			->setSubject('Message subject')
			->setTextBody('Plain text content')
			->send();

		$I = $this->tester;
		$I->seeEmailIsSent();
		$mail = $I->grabLastSentEmail();

		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey($emailTo);
	}

	public function testSendEmailWithHtml(): void {
		$emailTo = 'example@mail.com';

		Yii::$app->mailer->compose()
			->setFrom('foo@bar.com')
			->setTo($emailTo)
			->setSubject('Message subject')
			->setHtmlBody('<p>HTML Test</p>')
			->setTextBody('Plain text content')
			->send();

		$I = $this->tester;
		$I->seeEmailIsSent();
		$mail = $I->grabLastSentEmail();

		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey($emailTo);
	}

	public function testConnectionWithRealMailer(): void {
		$transport = $this->realMailer->getTransport();
		$ping = $transport->ping();

		$this->tester->assertTrue($ping);
	}

	public function testSendOnlineMailThroughRealMailer(): void {
		$emailTo = 'example@example.com.pl';
		$emailFrom = getenv('EMAIL_ROBOT');

		$message = $this->realMailer->compose();
		$message->setFrom($emailFrom)
			->setTo($emailTo)
			->setSubject('test subject')
			->setTextBody('test body');
		$isEmailSend = $message->send();

		$this->tester->assertTrue($isEmailSend);
	}

	public function testThrowsExceptionOnSendWithBadPortThroughRealMailer(): void {
		$emailTo = 'example@example.com.pl';
		$emailFrom = getenv('EMAIL_ROBOT');

		$transportSettings = $this->realTransportSettings;
		$transportSettings['port'] = 1; //Will never use port 1 really
		$this->realMailer->setTransport($transportSettings);

		$message = $this->realMailer->compose();
		$message->setFrom($emailFrom)
			->setTo($emailTo)
			->setSubject('test subject')
			->setTextBody('test body');

		$this->tester->expectThrowable(Swift_TransportException::class, function () use ($message) {
			$message->send();
		});
	}

}
