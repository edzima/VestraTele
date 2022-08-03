<?php

namespace common\modules\lead\models;

use common\modules\lead\Module;
use Yii;
use yii\db\ActiveRecord as BaseActiveRecord;
use yii\db\Connection;

abstract class ActiveRecord extends BaseActiveRecord {

	public static function getDb(): Connection {
		return Module::getInstance()->db ?: Yii::$app->getDb();
	}
}
