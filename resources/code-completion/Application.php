<?php

use common\components\Provisions;
use common\components\TaxComponent;
use common\formatters\Formatter;
use yii\BaseYii;

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
 * @property Provisions $provisions
 * @property TaxComponent $tax
 */
abstract class Application extends \yii\web\Application {

}
