<?php

use common\models\WojewodztwaSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel WojewodztwaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Regiony';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-index">

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'name',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
