<?php

use backend\helpers\Url;
use common\models\issue\SummonDocLink;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model SummonDocLink */
$summonLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['/summon/view', 'id' => $model->summon_id]);

?>
<div class="issue-user-change-for-settlement-with-provisions">
	<h1><?= Yii::t('issue', 'User: {user} mark Doc: {name} to Confirm.', [
			'user' => $model->doneUser->getFullName(),
			'name' => $model->doc->name,
		]) ?></h1>


	<p><?= Html::a(Html::encode($model->summon->getIssueName() . ' - ' . $model->summon->getTypeName()), $summonLink) ?></p>

</div>
