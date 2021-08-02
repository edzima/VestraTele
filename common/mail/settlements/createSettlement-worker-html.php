<?php

use common\helpers\Html;
use common\models\issue\IssueSettlement;

/* @var $this yii\web\View */
/* @var $settlement IssueSettlement */

?>
<div class="createSettlement-worker-email">
	<p>
		<?= Yii::t('settlement',
			'Create Settlement: {type} in Issue: {issue}.', [
				'type' => $settlement->getTypeName(),
				'issue' => $settlement->getIssueName(),
			]) ?>
	</p>
	<p>
	<p><?= Html::a($settlement->getTypeName(), $settlement->getFrontendUrl()) ?></p>
</div>
