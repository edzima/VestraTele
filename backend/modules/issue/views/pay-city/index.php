<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\issue\IssuePayCitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Terminy';
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['pay-calculation/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-city-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'city',
			'bank_transfer_at:monthDay',
			'direct_at:monthDay',
			'phone',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>


</div>
