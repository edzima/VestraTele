<?php

use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('settlement', 'Settlements without provisions');

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Without provisions');
?>
<div class="settlement-without-provisions">

	<?= $this->render('_grid', [
		'searchModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'withIssue' => true,
		'withCustomer' => true,
		'withProblemStatus' => false,
	]) ?>
</div>
