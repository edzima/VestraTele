<?php

use common\models\issue\IssuePayInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $pay IssuePayInterface */

$settlementLink = Yii::getAlias('@frontendUrl') . Yii::$app->urlManager->createUrl(['settlement/view', 'id' => $pay->getSettlementId()]);

?>
<?= Yii::t('settlement', 'Payment Pay: {value} in Settlement - {settlementType}.', [
	'value' => Yii::$app->formatter->asCurrency($pay->getValue()),
	'settlementType' => $pay->calculation->getTypeName(),
]) ?>>

<?= Html::a($pay->calculation->getTypeName(), $settlementLink) ?>

