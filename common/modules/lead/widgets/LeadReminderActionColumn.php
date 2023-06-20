<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\helpers\Url;
use common\modules\reminder\models\ReminderInterface;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\ReminderActionColumn;
use Yii;

class LeadReminderActionColumn extends ReminderActionColumn {

	public $controller = '/lead/reminder';

}
