<?php

use backend\helpers\Html;
use backend\models\search\PotentialClientSearch;
use backend\widgets\GridView;
use common\helpers\Url;
use common\models\PotentialClient;
use common\widgets\grid\ActionColumn;

/** @var yii\web\View $this */
/** @var PotentialClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('common', 'Potential Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="potential-client-index">

	<p>
		<?= Html::a(Yii::t('common', 'Create Potential Client'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'firstname',
			'lastname',
			//	'details:ntext',
			'city_id',
			//'birthday',
			//'status',
			//'created_at',
			//'updated_at',
			//'owner_id',
			'phone:tel',
			[
				'class' => ActionColumn::class,
				'urlCreator' => function ($action, PotentialClient $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
