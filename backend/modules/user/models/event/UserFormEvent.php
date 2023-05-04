<?php

namespace backend\modules\user\models\event;

use backend\modules\user\models\UserForm;
use yii\base\ModelEvent;

/**
 * {@inheritdoc}
 * @property UserForm $sender
 */
class UserFormEvent extends ModelEvent {

	public bool $isNewRecord;
}
