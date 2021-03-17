<?php

namespace backend\tests\Page\provision;

use backend\tests\Step\Functional\ProvisionManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;

class ProvisionUpdatePage {

	protected ProvisionFixtureHelper $provisionFixture;
	protected ProvisionManager $tester;

	public function __construct(ProvisionManager $I) {
		$this->tester = $I;
		$this->provisionFixture = new ProvisionFixtureHelper($this->tester);
	}

	public function haveFixtures(): void {
		$this->tester->haveFixtures(array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::users(),
				SettlementFixtureHelper::settlement(),
				SettlementFixtureHelper::pay(),
				ProvisionFixtureHelper::provision(),
				ProvisionFixtureHelper::type(),
			)
		);
	}

	public function haveProvision($value, array $attributes = []): int {
		return $this->provisionFixture->haveProvision($value, $attributes);
	}

	public function fillValueField(string $value) {
		$this->tester->fillField('ProvisionUpdateForm[value]', $value);
	}

	public function fillPercentField(string $value) {
		$this->tester->fillField('ProvisionUpdateForm[percent]', $value);
	}

}
