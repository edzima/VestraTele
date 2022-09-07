<?php

namespace frontend\tests;

use Codeception\Actor;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor {

	use _generated\ApiTesterActions;

	/**
	 * Define custom actions here
	 */

	public function amHeaderAuth(string $token, string $headerName = 'X-Api-Key'): void {
		$this->haveHttpHeader($headerName, $token);
	}
}
