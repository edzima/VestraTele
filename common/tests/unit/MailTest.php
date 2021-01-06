<?php

namespace common\tests\unit;

use Yii;
use yii\swiftmailer\Mailer;

class MailTest extends Unit {

	private Mailer $realMailer;

	public function _before() {
		parent::_before();
		$this->realMailer = new Mailer([
			'viewPath' => '@common/mail',
			'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'encryption' => getenv('EMAIL_ENCRYPTION'),
				'host' => getenv('EMAIL_SMTP_HOST'),
				'port' => getenv('EMAIL_SMTP_PORT'),
				'username' => getenv('EMAIL_USERNAME'),
				'password' => getenv('EMAIL_PASSWORD'),
				'timeout' => 5 //sec
			],
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


	public function testConnectionRealMailer(): void{
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

		$this->assertTrue($isEmailSend);
	}

}
