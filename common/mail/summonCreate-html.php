<?php

use common\helpers\Html;
use common\models\issue\Summon;

/* @var $this yii\web\View */
/* @var $model Summon */

$summonLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['/summon/view', 'id' => $model->id]);

?>
<div class="summon-create-email">

	<p><?= Html::encode($model->title) ?> </p>

	<p><?= Yii::t('common', 'Entity responsible') ?>: <?= Html::encode($model->entityWithCity) ?></p>

	<?= $this->render('_customerDetails-html', [
		'user' => $model->getIssueModel()->customer,
	]) ?>

	<p><?= Html::a(Yii::t('common', 'Details'), $summonLink) ?></p>
</div>
