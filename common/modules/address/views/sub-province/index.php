<?php

use common\models\address\search\SubProvinceSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel SubProvinceSearch */
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
			'id',
			'name',
			'state',
			'province',
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
