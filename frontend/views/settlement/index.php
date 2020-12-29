<?php

use frontend\models\search\IssuePayCalculationSearch;
use frontend\widgets\IssuePayCalculationGrid;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('frontend', 'Yours settlements');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= IssuePayCalculationGrid::widget([
		'filterModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'userProvisionsId' => Yii::$app->user->getId(),
	]) ?>
</div>
