<?php

namespace common\fixtures\helpers;

use common\fixtures\email\EmailTemplateFixture;
use common\fixtures\email\EmailTemplateTranslationFixture;
use Yii;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\entities\EmailTemplateTranslation;
use ymaker\email\templates\repositories\EmailTemplatesRepositoryInterface;

class EmailTemplateFixtureHelper extends BaseFixtureHelper {

	private const TEMPLATE = 'email.template';
	private const TRANSLATION = 'email.translation';

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
		return Yii::getAlias('@common/tests/_data/email-template/');
	}

	public static function fixture(): array {
		return [
			static::TEMPLATE => [
				'class' => EmailTemplateFixture::class,
				'dataFile' => static::getDefaultDataDirPath() . 'template.php',
			],
			static::TRANSLATION => [
				'class' => EmailTemplateTranslationFixture::class,
				'dataFile' => static::getDefaultDataDirPath() . 'translation.php',
			],
		];
	}

}
