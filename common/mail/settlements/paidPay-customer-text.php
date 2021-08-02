<?php

use common\models\issue\IssuePayInterface;
use common\models\user\Customer;

/* @var $this yii\web\View */
/* @var $customer Customer */
/* @var $pay IssuePayInterface */

?>

<?= Yii::t('common', 'Hello {firstname}', ['firstname' => $customer->profile->firstname]) ?>,
<?= Yii::t('settlement', 'Thank you for Paid Pay: {value}.', ['value' => Yii::$app->formatter->asCurrency($pay->getValue())]) ?>>

