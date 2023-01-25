<?php
namespace frontend\tests;
use common\tests\_support\UserRbacActor;

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
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;
    use UserRbacActor;

	/**
	 * Define custom actions here
	 *
	 * @param AcceptanceTester $I
	 * @throws \Exception
	 */

    public function waitForCalendarEventsLoaded(){
		$this->waitForElement('.fc-event', 5);
	}
}
