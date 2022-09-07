<?php

namespace common\modules\reminder;

use Closure;
use yii\base\Module as BaseModule;
use yii\db\ActiveRecord;

class Module extends BaseModule {

	public $controllerNamespace = 'common\modules\reminder\controllers';

	public string $relationName;
	public Closure $model;

	public function getModel(int $relatedId): ActiveRecord {
		return call_user_func($this->model, $relatedId);
	}

}
