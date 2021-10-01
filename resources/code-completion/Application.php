<?php

use common\components\EmailTemplateManager;
use common\components\HierarchyComponent;
use common\components\PayComponent;
use common\components\provision\Provisions;
use common\components\TaxComponent;
use common\components\User;
use common\components\Formatter;
use common\modules\lead\components\LeadManager;
use Edzima\Yii2Adescom\models\SenderInterface;
use yii\BaseYii;
use yii\queue\Queue;

/**
 * Fake class to define Yii 2.0 code completion for IDE.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class Yii extends BaseYii {

	/**
	 * @var Application
	 */
	public static $app;
}

/**
 * Fake class to define Yii 2.0 code completion for IDE.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 * @mixin yii\web\Application|yii\web\Application
 * @property Formatter $formatter
 * @property-read EmailTemplateManager $emailTemplate
 * @property HierarchyComponent $userHierarchy
 * @property LeadManager $leadManager
 * @property PayComponent $pay
 * @property Provisions $provisions
 * @property TaxComponent $tax
 * @property User $user
 * @property SenderInterface $sms
 * @property Queue $queue
 */
abstract class Application extends \yii\web\Application {

}
