<?php

namespace common\tests\unit;

use Swift_SmtpTransport;
use Swift_TransportException;
use yii\swiftmailer\Mailer;

class SmtpSwiftMailerTest extends Unit {

	private Mailer $mailer;

	public function _before(): void {
		parent::_before();

		$this->mailer = new Mailer([
			'transport' => [
				'class' => Swift_SmtpTransport::class,
				'encryption' => getenv('EMAIL_ENCRYPTION'),
				'host' => getenv('EMAIL_SMTP_HOST'),
				'port' => getenv('EMAIL_SMTP_PORT'),
				'username' => getenv('EMAIL_USERNAME'),
				'password' => getenv('EMAIL_PASSWORD'),
				'timeout' => 5 //sec
			],
		]);
	}

	private function getTransport(): Swift_SmtpTransport {
		return $this->mailer->getTransport();
	}

	public function testConnectionPing(): void {
		if (isset($_ENV['EMAIL_SMTP_HOST'])) {
			$transport = $this->getTransport();
			$ping = $transport->ping();
			$this->tester->assertTrue($ping);
		}
	}

	public function testThrowsExceptionOnSendWithBadPort(): void {
		$transport = $this->getTransport();
		$transport->setPort(1);
		$this->tester->assertFalse($transport->ping());
		$message = $this->mailer->compose();
		$message->setFrom('from@example.com')
			->setTo('to@example.com')
			->setSubject('test subject')
			->setTextBody('test body');

		$this->tester->expectThrowable(Swift_TransportException::class, function () use ($message) {
			$message->send();
		});
	}

}
