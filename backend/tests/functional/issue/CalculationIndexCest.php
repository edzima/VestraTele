<?php

namespace backend\tests\functional\issue;

use backend\tests\Step\Functional\IssueManager;

/**
 * Class CalculationIndexCest
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CalculationIndexCest {

	public function checkPage(IssueManager $I): void {
		$I->amOnPage('/issue/pay-calculation/index');
	}
}
