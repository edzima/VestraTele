<?php

use common\models\issue\IssuePayInterface;
use common\models\user\Customer;

/* @var $this yii\web\View */
/* @var $customer Customer */
/* @var $pay IssuePayInterface */

?>
<div class="paidPay-customer-email">
	<p><?= Yii::t('common', 'Hello {firstname}', ['firstname' => $customer->profile->firstname]) ?>,</p>

	<p><?= Yii::t('settlement', 'Thank you for Paid Pay: {value}.', ['value' => Yii::$app->formatter->asCurrency($pay->getValue())]) ?>></p>
</div>
