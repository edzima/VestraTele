<?php

use common\helpers\ArrayHelper;
use common\models\provision\Provision;
use common\models\user\User;
use Decimal\Decimal;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

/**
 * @var Provision[] $provisions
 */
$provisions = $dataProvider->getModels();
$users = ArrayHelper::index($provisions, null, 'to_user_id');

$usersNames = User::getSelectList(array_keys($users), false);
$totalSum = new Decimal(0);
foreach ($users as $userId => $userProvisions) {
	$sum = new Decimal(0);
	foreach ($userProvisions as $provision) {
		$sum = $sum->add($provision->getValue());
	}
	if ($sum > 0) {
		echo $usersNames[$userId]
			. " - " . Yii::$app->formatter->asCurrency($sum)
			. ' (' . count($userProvisions) . ')<br><br>';
	}
	$totalSum = $totalSum->add($sum);
}

if ($totalSum > 0) {
	echo Yii::t('provision', 'Sum {value}', [
		'value' => Yii::$app->formatter->asCurrency($totalSum),
	]);
}

?>
