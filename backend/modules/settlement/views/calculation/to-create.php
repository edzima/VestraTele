<?php

use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $searchModel IssueToCreateCalculationSearch */
/* @var $dataProvider ActiveDataProvider|null */

$this->title = Yii::t('backend', 'Issues to create calculations');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="issue-pay-calculation-new">
	<?= $this->render('_to-create_grid', [
		'searchModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'withIssue' => true,
		'withCustomer' => true,
	]) ?>
</div>
