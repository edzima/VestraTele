<?php

use common\models\issue\IssueSettlement;
use common\models\user\Customer;

/* @var $this yii\web\View */
/* @var $customer Customer */
/* @var $settlement IssueSettlement */

?>
<div class="createSettlement-customer-email">
	<p><?= Yii::t('common', 'Hello {firstname}', ['firstname' => $customer->profile->firstname]) ?>,</p>

	<p><?= Yii::t('settlement',
			'You have new Settlement: {type} with Value: {value}.', [
				'type' => $settlement->getTypeName(),
				'value' => Yii::$app->formatter->asCurrency($settlement->getValue()),
			]) ?>>
	</p>
</div>
