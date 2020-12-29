<?php

use backend\modules\settlement\models\search\IssuePaySearch;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssuePaySearch */
/* @var $dataProvider ActiveDataProvider */

?>

<div class="pay-summary-wrap">
	<h4>Podsumowanie płatności</h4>
	<ul>
		<li>
			Należna: <?= Yii::$app->formatter->asCurrency($searchModel->getValueSum($dataProvider->query)) ?></li>
		<li>
			Zapłacono: <?= Yii::$app->formatter->asCurrency($searchModel->getPayedSum($dataProvider->query)) ?></li>
		<li>
			Niezaplacono: <?= Yii::$app->formatter->asCurrency($searchModel->getNotPaySum($dataProvider->query)) ?></li>
	</ul>
</div>
