<?php

use common\models\user\Worker;
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

	<p>
		<?= Html::a(Yii::t('settlement', 'Pays'), '/pay/index', ['class' => 'btn btn-success']) ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_PAY_RECEIVED)
			? Html::a(Yii::t('settlement', 'Received pays'), '/pay-received/index', ['class' => 'btn btn-primary'])
			: ''
		?>
	</p>

	<?= IssuePayCalculationGrid::widget([
		'filterModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'userProvisionsId' => Yii::$app->user->getId(),
	]) ?>
</div>
