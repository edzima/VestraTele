<?php

use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use common\models\user\User;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('settlement', 'Provision Control');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];

if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Settlements'), 'url' => ['/settlement/calculation/index']];
}

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="settlement-calculation-problem-provision-control">

	<?= IssuePayCalculationGrid::widget([
		'filterModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'rowColors' => false,
	]) ?>

</div>

