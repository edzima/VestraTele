<?php

namespace backend\tests\Step\Functional;

use backend\tests\functional\settlement\CalculationCreateCest;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\user\User;

/**
 * Class CreateCalculationIssueManager
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CreateCalculationIssueManager extends IssueManager {

	protected function getUsername(): string {
		return 'create-calculation-issue-manager';
	}

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_CALCULATION_TO_CREATE]);
	}

	public function amOnCreatePage(int $issueId = 1, int $typeId = SettlementFixtureHelper::TYPE_ID_HONORARIUM, array $params = []): void {
		$params['issueId'] = $issueId;
		$params['typeId'] = $typeId;
		$this->amOnRoute(CalculationCreateCest::ROUTE, $params);
	}
}
