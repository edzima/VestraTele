<?php

namespace frontend\tests\unit\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\helpers\ArrayHelper;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;
use frontend\models\search\IssueSearch;
use frontend\tests\unit\Unit;
use yii\base\InvalidConfigException;

/**
 * @property IssueSearch $model
 * @method IssueInterface[] getModels(array $params = [])
 */
class IssueSearchTest extends Unit {

	use UnitSearchModelTrait;

	protected function _before() {
		$this->tester->haveFixtures(IssueFixtureHelper::fixtures());
		$this->model = $this->createModel();
		parent::_before();
	}

	public function testSearchWithotUserId(): void {
		$model = new IssueSearch();
		$this->tester->expectThrowable(InvalidConfigException::class, function () use ($model) {
			$model->search([]);
		});
	}

	public function testDoubleSameUserInIssue(): void {
		$this->model->user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$models = $this->getModels();
		$this->tester->assertNotEmpty($models);
		$this->tester->assertSameSize($models, ArrayHelper::index($models, 'issueId'));
		$model = reset($models);
		$model->getIssueModel()->linkUser(UserFixtureHelper::AGENT_PETER_NOWAK, IssueUser::TYPE_CO_AGENT);
		$this->tester->assertSameSize($models, $this->getModels());
	}

	protected function createModel(): SearchModel {
		return new IssueSearch();
	}
}
