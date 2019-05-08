<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\issue\IssueTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rodzaje';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-type-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'name',
			'short_name',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
