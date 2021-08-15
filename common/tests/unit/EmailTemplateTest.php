<?php

namespace common\tests\unit;

use backend\helpers\EmailTemplateKeyHelper;
use common\components\EmailTemplateManager;
use common\fixtures\helpers\EmailTemplateFixtureHelper;
use Yii;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\entities\EmailTemplateTranslation;
use ymaker\email\templates\repositories\EmailTemplatesRepository;

class EmailTemplateTest extends Unit {

	private EmailTemplateFixtureHelper $fixture;
	private EmailTemplatesRepository $repository;
	private EmailTemplateManager $manager;

	public function _before() {
		$this->repository = new EmailTemplatesRepository();
		$this->manager = new EmailTemplateManager($this->repository);
		$this->fixture = new EmailTemplateFixtureHelper($this->tester);
		$this->fixture->setRepository($this->repository);

		parent::_before();
	}

	public function _fixtures(): array {
		return EmailTemplateFixtureHelper::fixture();
	}

	public function testSave(): void {
		$this->fixture->save('issue.create', 'Issue Create', 'Issue Create Body');
		$this->tester->seeRecord(EmailTemplate::class, [
			'key' => 'issue.create',
		]);
		$this->tester->seeRecord(EmailTemplateTranslation::class, [
			'language' => Yii::$app->language,
			'subject' => 'Issue Create',
			'body' => 'Issue Create Body',
		]);
	}

	public function testLikeKeySearch(): void {
		$this->fixture->save('issue.create');
		$this->fixture->save('issue.update');

		$this->tester->assertCount(1, $this->getTemplatesLikeKey('issue.create'));
		$this->tester->assertCount(1, $this->getTemplatesLikeKey('issue.update'));
		$this->tester->assertCount(2, $this->getTemplatesLikeKey('issue.'));
		$this->tester->assertNull($this->getTemplatesLikeKey('not-existed'));
	}

	public function testIssueTypesKeys(): void {
		$this->fixture->save(
			'issue.create.' . EmailTemplateKeyHelper::issueTypesKeyPart([1, 2])
		);
		$this->fixture->save(
			'issue.create.' . EmailTemplateKeyHelper::issueTypesKeyPart([1, 3]),
		);
		$this->fixture->save(
			'issue.create.' . EmailTemplateKeyHelper::issueTypesKeyPart([3]),
		);

		$models = $this->getTemplatesLikeKey('issue.create');

		$modelsType1 = array_filter($models, static function (string $key): bool {
			return EmailTemplateKeyHelper::isForIssueType($key, 1);
		}, ARRAY_FILTER_USE_KEY);
		$modelsType2 = array_filter($models, static function (string $key): bool {
			return EmailTemplateKeyHelper::isForIssueType($key, 2);
		}, ARRAY_FILTER_USE_KEY);

		$modelsType3 = array_filter($models, static function (string $key): bool {
			return EmailTemplateKeyHelper::isForIssueType($key, 3);
		}, ARRAY_FILTER_USE_KEY);
		$modelsType4 = array_filter($models, static function (string $key): bool {
			return EmailTemplateKeyHelper::isForIssueType($key, 4);
		}, ARRAY_FILTER_USE_KEY);

		$this->tester->assertCount(2, $modelsType1);
		$this->tester->assertNotNull($this->getIssueTypeTemplatesLikeKey('issue.create', 1));

		$this->tester->assertCount(1, $modelsType2);
		$this->tester->assertNotNull($this->getIssueTypeTemplatesLikeKey('issue.create', 2));

		$this->tester->assertCount(2, $modelsType3);
		$this->tester->assertNotNull($this->getIssueTypeTemplatesLikeKey('issue.create', 3));

		$this->tester->assertCount(0, $modelsType4);
		$this->tester->assertNull($this->getIssueTypeTemplatesLikeKey('issue.create', 4));
	}

	private function getTemplatesLikeKey(string $key): ?array {
		return $this->manager->getTemplatesLikeKey($key);
	}

	private function getIssueTypeTemplatesLikeKey(string $key, int $id): ?\ymaker\email\templates\models\EmailTemplate {
		return $this->manager->getIssueTypeTemplatesLikeKey($key, $id);
	}
}
