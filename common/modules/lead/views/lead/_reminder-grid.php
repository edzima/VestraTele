<?php

use common\helpers\Url;
use common\models\issue\Summon;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\widgets\LeadReminderActionColumn;
use common\modules\reminder\models\ReminderQuery;
use common\modules\reminder\widgets\ReminderGridModal;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $model ActiveLead */
/* @var $dataProvider DataProviderInterface */
/* @var $onlyUser bool */
?>

<?= ReminderGridModal::widget([
	'dataProvider' => $dataProvider,
	'controller' => '/lead/reminder',
	'createUrl' => Url::to(['/lead/reminder/create', 'id' => $model->getId()]),
	'reminderGridOptions' => [
		'visibleUserColumn' => !$onlyUser,
		'actionColumn' => [
			'class' => LeadReminderActionColumn::class,
			'template' => '{not-done} {done} {update} {delete}',
		],
		'showOnEmpty' => true,
	],
]) ?>

