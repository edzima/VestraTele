<?php

use common\components\callpage\CallPageClient;
use common\components\Formatter;
use common\components\HierarchyComponent;
use common\components\IssueTypeUser;
use common\components\keyStorage\KeyStorage;
use common\components\message\MessageTemplateManager;
use common\components\PayComponent;
use common\components\postal\PocztaPolska;
use common\components\provision\Provisions;
use common\components\rbac\ManagerFactory;
use common\components\TaxComponent;
use common\components\User;
use common\modules\file\components\FileAuth;
use common\modules\lead\components\LeadClient;
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
 * @property CallPageClient|null $callPageClient
 * @property Formatter $formatter
 * @property LeadClient $leadClient
 * @property IssueTypeUser $issueTypeUser
 * @property-read MessageTemplateManager $messageTemplate
 * @property HierarchyComponent $userHierarchy
 * @property PayComponent $pay
 * @property Provisions $provisions
 * @property TaxComponent $tax
 * @property User $user
 * @property SenderInterface $sms
 * @property Queue $queue
 * @property KeyStorage $keyStorage
 * @property PocztaPolska $pocztaPolska
 * @property FileAuth $fileAuth
 * @property ManagerFactory $accessManagerFactory
 */
abstract class Application extends \yii\web\Application {

}
