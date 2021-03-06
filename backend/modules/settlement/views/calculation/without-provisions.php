<?php

use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
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

	<?= IssuePayCalculationGrid::widget([
		'filterModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'withProblems' => false,
	]) ?>
</div>
