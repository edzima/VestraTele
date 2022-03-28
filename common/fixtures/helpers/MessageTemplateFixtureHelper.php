<?php

namespace common\fixtures\helpers;

use common\fixtures\message\MessageTemplateFixture;
use common\fixtures\message\MessageTemplateTranslationFixture;
use Yii;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\entities\EmailTemplateTranslation;
use ymaker\email\templates\repositories\EmailTemplatesRepository;
use ymaker\email\templates\repositories\EmailTemplatesRepositoryInterface;

class MessageTemplateFixtureHelper extends BaseFixtureHelper {

	public const DIR_ISSUE_CREATE = 'issue-create';
	public const DIR_ISSUE_STAGE_CHANGE = 'issue-stage-change';

	public const DIR_ISSUE_PAY_PAYED = 'issue-pay-payed';
	public const DIR_ISSUE_PAY_DEMAND = 'issue-pay-demand';
	public const DIR_ISSUE_SETTLEMENT_CREATE = 'issue-settlement-create';

	private const TEMPLATE = 'message.template';
	private const TRANSLATION = 'message.translation';

	private ?EmailTemplatesRepositoryInterface $repository = null;

	public function setRepository(EmailTemplatesRepositoryInterface $repository): void {
		$this->repository = $repository;
	}

	public function getRepository(): EmailTemplatesRepositoryInterface {
		if ($this->repository === null) {
			$this->repository = new EmailTemplatesRepository();
		}
		return $this->repository;
	}

	public function save(string $key, string $subject = 'Test Subject', string $body = 'Test Body', string $hint = 'Test Hint', string $language = null): void {
		$language = $language ?: Yii::$app->language;
		$this->getRepository()->create();
		$template = new EmailTemplate(['key' => $key]);
		$template->save();
		$this->repository->save($template, [
			EmailTemplateTranslation::internalFormName() => [
				$language => [
					'subject' => $subject,
					'body' => $body,
					'hint' => $hint,
				],
			],
		]);
	}

	public function flushAll(): void {
		EmailTemplate::deleteAll();
	}

	protected static function getDefaultDataDirPath(string $dir = null): string {
		$path = '@common/tests/_data/message-template/';
		if ($dir) {
			$path .= DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
		}
		return Yii::getAlias($path);
	}

	public static function fixture(string $dir = null): array {
		return [
			static::TEMPLATE => [
				'class' => MessageTemplateFixture::class,
				'dataFile' => static::getDefaultDataDirPath($dir) . 'template.php',
			],
			static::TRANSLATION => [
				'class' => MessageTemplateTranslationFixture::class,
				'dataFile' => static::getDefaultDataDirPath($dir) . 'translation.php',
			],
		];
	}

}
