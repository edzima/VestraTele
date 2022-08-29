<?php

namespace common\tests;

use Codeception\Actor;
use Codeception\Lib\Friend;
use common\fixtures\helpers\FixtureTester;
use common\models\message\Message;

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
 * @method Friend haveFriend($name, $actorClass = null)
 * @method grabFixture($name, $index = null)
 * @method seeRecord(string $class, array $array)
 *
 * @SuppressWarnings(PHPMD)
 */
class UnitTester extends Actor implements FixtureTester {

	use _generated\UnitTesterActions;

	/**
	 * Define custom actions here
	 */

	public function assertMessageBodyContainsString(string $text, Message $message): void {
		$body = $message->getHtmlBody();
		codecept_debug($body);
		$this->assertStringContainsString($text, $body);
	}

	public function assertMessageBodyNotContainsString(string $text, Message $message): void {
		$body = $message->getBody();
		$this->assertStringNotContainsString($text, $body);
	}
}
