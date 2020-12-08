<?php

use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use common\models\user\User;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('settlement', 'Settlements');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-calculation-index">

	<p>
		<?= Html::a(Yii::t('backend', 'To create'), ['to-create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('backend', 'With problems'), ['/settlement/calculation-problem/index'], ['class' => 'btn btn-warning']) ?>

		<?= Yii::$app->user->can(User::PERMISSION_PROVISION)
			? Html::a(Yii::t('backend', 'Without provisions'), ['without-provisions'], ['class' => 'btn btn-info'])
			: '' ?>

	</p>

	<?= IssuePayCalculationGrid::widget([
		'filterModel' => $searchModel,
		'dataProvider' => $dataProvider,
	]) ?>

</div>
