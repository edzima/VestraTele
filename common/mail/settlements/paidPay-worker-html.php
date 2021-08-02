<?php

use common\models\issue\IssuePayInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $pay IssuePayInterface */

?>
<div class="paidPay-worker-email">
	<p><?= Yii::t('settlement', 'Payment is Paid: {value}.', ['value' => Yii::$app->formatter->asCurrency($pay->getValue())]) ?>></p>

	<p><?= Html::a($pay->calculation->getTypeName(), $pay->calculation->getFrontendUrl()) ?></p>
</div>
