<?php

use common\helpers\Html;
use common\models\issue\Summon;

/* @var $this yii\web\View */
/* @var $model Summon */

$summonLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['/summon/view', 'id' => $model->id]);

$this->title = $model->title;
$this->params['primaryButtonText'] = Yii::t('common', 'Details');
$this->params['primaryButtonHref'] = $summonLink;
?>
<div class="summon-create-email">

	<?php if ($model->entityWithCity): ?>
		<p><?= Yii::t('common', 'Entity responsible') ?>: <?= Html::encode($model->entityWithCity) ?></p>
	<?php endif; ?>

	<?= $this->render('_customerDetails-html', [
		'user' => $model->getIssueModel()->customer,
	]) ?>

</div>
