<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\message\IssueMessagesForm;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use Yii;
use yii\base\Model;

abstract class BaseIssueMessagesFormTest extends Unit {

	use UnitModelTrait;

	protected const MODEL_CLASS = IssueMessagesForm::class;
	protected const DEFAULT_ISSUE_TYPE = 1;
	protected const DEFAULT_SMS_OWNER_ID = UserFixtureHelper::AGENT_EMILY_PAT;

	protected ?IssueInterface $issue = null;
	protected IssueMessagesForm $model;

	protected MessageTemplateFixtureHelper $templateFixture;
	protected IssueFixtureHelper $issueFixture;

	public function _before(): void {
		parent::_before();
		$this->templateFixture = new MessageTemplateFixtureHelper($this->tester);
		$this->issueFixture = new IssueFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(true),
			MessageTemplateFixtureHelper::fixture($this->messageTemplateFixtureDir()),
		);
	}

	abstract protected function messageTemplateFixtureDir(): string;

	/**
	 * @dataProvider keysProvider
	 * @param string $generated
	 * @param string $expected
	 */
	public function testKeys(string $generated, string $expected): void {
		$this->tester->assertSame($expected, $generated);
	}

	abstract public function keysProvider(): array;

	public function testNotWorkersIssueUsersTypes(): void {
		$this->giveModel();
		$this->model->workersTypes = [IssueUser::TYPE_CUSTOMER];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Issue Workers Types is invalid.', 'workersTypes');
	}

	public function testNotIssueWorkerType(): void {
		$this->giveModel();
		$this->model->workersTypes = [IssueUser::TYPE_TELEMARKETER];
		$this->thenSuccessValidate(['workersTypes']);
		$this->issue->getIssueModel()->unlinkUser(IssueUser::TYPE_TELEMARKETER);
		$this->issue->getIssueModel()->refresh();
		$this->giveModel();
		$this->model->workersTypes = [IssueUser::TYPE_TELEMARKETER];
		$this->thenUnsuccessValidate(['workersTypes']);
	}

	public function testEmptySmsOwnerWhenSmsEnable(): void {
		$this->giveModel();
		$this->model->sms_owner_id = null;
		$this->model->sendSmsToCustomer = true;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('SMS Owner cannot be blank when want send SMS.', 'sms_owner_id');
	}

	public function testSmsToCustomerWithoutTemplates(): void {
		$this->templateFixture->flushAll();
		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertNull($sms);
	}

	public function testSmsToAgentWithoutTemplates(): void {
		$this->templateFixture->flushAll();
		$this->giveModel();
		$sms = $this->model->getSmsToAgent();
		$this->tester->assertNull($sms);
	}

	public function getModel(): Model {
		return $this->model;
	}

	protected function giveModel(IssueInterface $issue = null, array $config = []): void {
		if ($issue === null) {
			if ($this->issue === null) {
				$this->giveIssue();
			}
			$issue = $this->issue;
		}
		if (!isset($config['sms_owner_id'])) {
			$config['sms_owner_id'] = static::DEFAULT_SMS_OWNER_ID;
		}
		$config['issue'] = $issue;
		$config = array_merge($config, $this->getModelDefaultConfig());
		$this->model = Yii::createObject($config);
	}

	protected function getModelDefaultConfig(): array {
		return [
			'class' => static::MODEL_CLASS,
		];
	}

	protected function giveIssue(int $type = self::DEFAULT_ISSUE_TYPE): void {
		$this->issue = Issue::find()->andWhere(['type_id' => $type])->one();
	}
}
