<?php

use backend\modules\settlement\models\search\IssuePayCalculationSearch;
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
		<?= Yii::$app->user->can(User::PERMISSION_PROVISION)
			? Html::a(Yii::t('backend', 'Without provisions'), ['without-provisions'], ['class' => 'btn btn-info'])
			: '' ?>

	</p>

	<?= $this->render('_grid', [
		'searchModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'withIssue' => true,
		'withCustomer' => true,
	]) ?>
</div>
