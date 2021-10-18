<?php

namespace common\fixtures\helpers;

use common\fixtures\message\MessageTemplateFixture;
use common\fixtures\message\MessageTemplateTranslationFixture;
use Yii;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\entities\EmailTemplateTranslation;
use ymaker\email\templates\repositories\EmailTemplatesRepositoryInterface;

class MessageTemplateFixtureHelper extends BaseFixtureHelper {

	private const TEMPLATE = 'message.template';
	private const TRANSLATION = 'message.translation';

	private EmailTemplatesRepositoryInterface $repository;

	public function setRepository(EmailTemplatesRepositoryInterface $repository): void {
		$this->repository = $repository;
	}

	public function save(string $key, string $subject = 'Test Subject', string $body = 'Test Body', string $language = null): void {
		$language = $language ?: Yii::$app->language;
		$this->repository->create();
		$template = new EmailTemplate(['key' => $key]);
		$template->save();
		$this->repository->save($template, [
			EmailTemplateTranslation::internalFormName() => [
				$language => [
					'subject' => $subject,
					'body' => $body,
				],
			],
		]);
	}

	protected static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/message-template/');
	}

	public static function fixture(): array {
		return [
			static::TEMPLATE => [
				'class' => MessageTemplateFixture::class,
				'dataFile' => static::getDefaultDataDirPath() . 'template.php',
			],
			static::TRANSLATION => [
				'class' => MessageTemplateTranslationFixture::class,
				'dataFile' => static::getDefaultDataDirPath() . 'translation.php',
			],
		];
	}

}
