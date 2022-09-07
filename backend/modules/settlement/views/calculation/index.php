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
		<?= Html::a(Yii::t('settlement', 'Uncollectible'), ['/settlement/calculation-problem/index'], ['class' => 'btn btn-warning']) ?>

		<?= Yii::$app->user->can(User::PERMISSION_PROVISION)
			? Html::a(Yii::t('backend', 'Without provisions'), ['without-provisions'], ['class' => 'btn btn-info'])
			: ''
		?>

		<?= Yii::$app->user->can(User::PERMISSION_COST)
			? Html::a(Yii::t('backend', 'Costs'), ['/settlement/cost/index'], ['class' => 'btn btn-warning'])
			: ''
		?>


		<?= Yii::$app->user->can(User::PERMISSION_PAY)
			? Html::a(Yii::t('settlement', 'Pays'), ['/settlement/pay/index'], ['class' => 'btn btn-primary'])
			: ''
		?>



		<?= Yii::$app->user->can(User::PERMISSION_PROVISION)
			? Html::a(Yii::t('provision', 'Delete provisions'),
				['/provision/settlement/delete-multi', 'ids' => $dataProvider->getKeys()],
				[
					'class' => 'btn btn-danger pull-right',
					'data' => [
						'confirm' => Yii::t('provision', 'Are you sure you want to delete provisions for this settlements?'),
						'method' => 'post',
					],
				])
			: ''
		?>


	</p>

	<?= IssuePayCalculationGrid::widget([
		'filterModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'withProblems' => false,
	]) ?>

</div>
