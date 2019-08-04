<?php

use common\models\WojewodztwaSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel WojewodztwaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gminy';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-province-index">

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'name',
			'region',
			'state',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
