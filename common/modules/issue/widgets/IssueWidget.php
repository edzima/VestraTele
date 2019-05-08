<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:06
 */

namespace common\modules\issue\widgets;

use common\models\issue\Issue;
use yii\base\InvalidConfigException;
use yii\base\Widget;

abstract class IssueWidget extends Widget {

	public $model;

	public function init() {
		if (!$this->model instanceof Issue) {
			throw new InvalidConfigException('$model must be instance of: ' . Issue::class);
		}
		parent::init();
	}
}