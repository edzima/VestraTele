<?php

use common\helpers\Url;
use common\models\issue\Summon;
use common\modules\reminder\models\ReminderQuery;
use common\modules\reminder\widgets\ReminderGridModal;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

/* @var $this yii\web\View */
/* @var $model Summon */
/* @var $controller string */
/* @var $dataProvider DataProviderInterface */
?>

<?= ReminderGridModal::widget([
	'dataProvider' => new ActiveDataProvider([
		'query' => $model->getRemindersLink()
			->joinWith([
				'reminder' => function (ReminderQuery $query) {
					$query->orderByDateAndPriority();
				},
			]),
	]),
	'controller' => '/reminder/summon',
	'createUrl' => Url::to(['/reminder/summon/create', 'id' => $model->id]),
]) ?>

