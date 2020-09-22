<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:06
 */

namespace common\modules\issue\widgets;

use common\models\issue\Issue;
use yii\base\Widget;

abstract class IssueWidget extends Widget {

	public Issue $model;

}
