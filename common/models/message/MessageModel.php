<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\components\message\MessageTemplateManager;
use Yii;
use yii\base\Model;
use yii\di\Instance;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;

abstract class MessageModel extends Model {

	public const TYPE_EMAIL = MessageTemplateKeyHelper::TYPE_EMAIL;
	public const TYPE_SMS = MessageTemplateKeyHelper::TYPE_SMS;

	/**
	 * @var string|array|MessageTemplateManager
	 */
	public $templateManager = 'messageTemplate';

	/**
	 * @var string|array|MailerInterface
	 */
	public $mailer = 'mailer';

	public ?string $language = null;
	public string $smsClass;

	public ?string $fromEmail = null;
	public ?string $fromNameTemplate = '{appName}';

	public function init() {
		parent::init();
		if ($this->language === null) {
			$this->language = Yii::$app->language;
		}
	}

	public function createSms(array $config = [], MessageTemplate $template = null): QueueSmsForm {
		$config['class'] = $this->smsClass;
		/**
		 * @var QueueSmsForm $model
		 */
		$model = Yii::createObject($config);
		if ($template) {
			$model->message = $template->getSmsMessage();
		}
		return $model;
	}

	public function createEmail(MessageTemplate $template = null): MessageInterface {
		$message = $this->getMailer()->compose();
		if ($template) {
			$this->bindEmailTemplate($message, $template);
		}
		$from = $this->getFromMailer();
		if (!empty($from)) {
			$message->setFrom($from);
		}
		return $message;
	}

	protected function getFromMailer(): ?array {
		if (empty($this->fromEmail)) {
			$this->fromEmail = Yii::$app->params['supportEmail'];
		}
		$name = $this->getFromName();
		if ($name === null) {
			return null;
		}
		return [
			$this->fromEmail => $name,
		];
	}

	protected function getFromName(): ?string {
		if (empty($this->fromNameTemplate)) {
			return null;
		}
		return strtr($this->fromNameTemplate, [
			'{appName}' => Yii::$app->name,
		]);
	}

	protected function bindEmailTemplate(MessageInterface $message, MessageTemplate $template): void {
		$message->setSubject($template->getSubject());
		$message->setHtmlBody($template->getBody());
	}

	protected function getSmsTemplate(string $baseKey): ?MessageTemplate {
		return $this->getTemplate(static::TYPE_SMS, $baseKey);
	}

	protected function getEmailTemplate(string $baseKey): ?MessageTemplate {
		return $this->getTemplate(static::TYPE_EMAIL, $baseKey);
	}

	protected function getTemplate(string $type, string $baseKey): ?MessageTemplate {
		$templates = $this
			->getTemplateManager()
			->getTemplatesLikeKey(static::generateKey($type, $baseKey), $this->language);
		if ($templates) {
			return reset($templates);
		}
		return null;
	}

	protected function getTemplateManager(): MessageTemplateManager {
		if (!is_object($this->templateManager)) {
			$this->templateManager = Instance::ensure($this->templateManager, MessageTemplateManager::class);
		}
		return $this->templateManager;
	}

	protected function getMailer(): MailerInterface {
		if (!is_object($this->mailer)) {
			$this->mailer = Instance::ensure($this->mailer, MailerInterface::class);
		}
		return $this->mailer;
	}

	public static function generateKey(string $type, string $key): string {
		$parts = [$type, $key];
		return MessageTemplateKeyHelper::generateKey($parts);
	}

}
