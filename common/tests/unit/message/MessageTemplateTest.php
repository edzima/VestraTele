<?php

namespace common\tests\unit\message;

use common\components\message\MessageTemplate;
use common\components\message\MessageTemplateKeyHelper;
use common\components\message\MessageTemplateManager;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\tests\unit\Unit;
use Yii;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\entities\EmailTemplateTranslation;
use ymaker\email\templates\repositories\EmailTemplatesRepository;

class MessageTemplateTest extends Unit {

	private MessageTemplateFixtureHelper $fixture;
	private EmailTemplatesRepository $repository;
	private MessageTemplateManager $manager;

	public function _before() {
		$this->repository = new EmailTemplatesRepository();
		$this->manager = new MessageTemplateManager($this->repository);
		$this->fixture = new MessageTemplateFixtureHelper($this->tester);
		$this->fixture->setRepository($this->repository);
		parent::_before();
	}

	public function _fixtures(): array {
		return MessageTemplateFixtureHelper::fixture();
	}

	public function testSave(): void {
		$this->fixture->save('test.first', 'Test Some Subject', 'Test Some Body');
		$this->tester->seeRecord(EmailTemplate::class, [
			'key' => 'test.first',
		]);
		$this->tester->seeRecord(EmailTemplateTranslation::class, [
			'language' => Yii::$app->language,
			'subject' => 'Test Some Subject',
			'body' => 'Test Some Body',
		]);
	}

	public function testSmsMessageBreakLine(): void {
		$template = new MessageTemplate(
			'Test Subject',
			'<p>Hello,</p><p>Your issue is Create. <br>Tel us:123-123-123</p>'
		);
		$this->tester->assertSame("Hello,\nYour issue is Create. \nTel us:123-123-123", $template->getSmsMessage());
	}

	public function testRemoveNBSP(): void {
		$template = new MessageTemplate('Test Subject with NBSP in Body',
			"3 000,00 zl");
		$this->tester->assertSame('3 000,00 zl', $template->getBody());
		$this->tester->assertSame('3 000,00 zl', $template->getSmsMessage());
	}

	public function testLikeKeySearch(): void {
		$this->fixture->save('test.first');
		$this->fixture->save('test.double');

		$this->tester->assertCount(1, $this->getTemplatesLikeKey('test.first'));
		$this->tester->assertCount(1, $this->getTemplatesLikeKey('test.double'));
		$this->tester->assertCount(2, $this->getTemplatesLikeKey('test.'));
		$this->tester->assertNull($this->getTemplatesLikeKey('not-existed'));
	}

	public function testIssueTypesKeys(): void {
		$this->fixture->flushAll();
		$this->fixture->save(
			'issue.create.' . MessageTemplateKeyHelper::issueTypesKeyPart([1, 2])
		);
		$this->fixture->save(
			'issue.create.' . MessageTemplateKeyHelper::issueTypesKeyPart([1, 3]),
		);
		$this->fixture->save(
			'issue.create.' . MessageTemplateKeyHelper::issueTypesKeyPart([3]),
		);

		$models = $this->getTemplatesLikeKey('issue.create');

		$modelsType1 = array_filter($models, static function (string $key): bool {
			return MessageTemplateKeyHelper::isForIssueType($key, 1);
		}, ARRAY_FILTER_USE_KEY);
		$modelsType2 = array_filter($models, static function (string $key): bool {
			return MessageTemplateKeyHelper::isForIssueType($key, 2);
		}, ARRAY_FILTER_USE_KEY);

		$modelsType3 = array_filter($models, static function (string $key): bool {
			return MessageTemplateKeyHelper::isForIssueType($key, 3);
		}, ARRAY_FILTER_USE_KEY);
		$modelsType4 = array_filter($models, static function (string $key): bool {
			return MessageTemplateKeyHelper::isForIssueType($key, 4);
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

	private function getIssueTypeTemplatesLikeKey(string $key, int $id): ?MessageTemplate {
		return $this->manager->getIssueTypeTemplatesLikeKey($key, $id);
	}
}
