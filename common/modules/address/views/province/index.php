<?php

use common\models\address\search\ProvinceSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel ProvinceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('address', 'Create province');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="province-index">

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			[
				'attribute' => 'wojewodztwo',
				'value' => 'wojewodztwo.name',
				'label' => 'Województwo',
			],
			'name',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>
