<?php

use common\helpers\Html;
use common\models\issue\IssueSettlement;

/* @var $this yii\web\View */
/* @var $settlement IssueSettlement */

?>

<?= Yii::t('settlement',
	'Create Settlement: {type} in Issue: {issue}.', [
		'type' => $settlement->getTypeName(),
		'issue' => $settlement->getIssueName(),
	]) ?>

<p><?= $settlement->getFrontendUrl() ?>

