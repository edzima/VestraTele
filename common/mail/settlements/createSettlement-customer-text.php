<?php

use common\models\issue\IssueSettlement;
use common\models\user\Customer;

/* @var $this yii\web\View */
/* @var $customer Customer */
/* @var $settlement IssueSettlement */

?>
<?= Yii::t('common', 'Hello {firstname}', ['firstname' => $customer->profile->firstname]) ?>,

<?= Yii::t('settlement',
	'You have new Settlement: {type} with Value: {value}.', [
		'type' => $settlement->getTypeName(),
		'value' => Yii::$app->formatter->asCurrency($settlement->getValue()),
	]) ?>>
