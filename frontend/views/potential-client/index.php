<?php

use common\models\PotentialClient;
use common\widgets\grid\ActionColumn;
use frontend\helpers\Html;
use frontend\models\search\PotentialClientSearch;
use frontend\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel PotentialClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Potential Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="potential-client-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('common', 'Create Potential Client'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => PotentialClientSearch::getStatusesNames(),
			],
			'name',
			'details:ntext',
			'cityName',
			'birthday',
			//'created_at',
			'updated_at:date',

			[
				'class' => ActionColumn::class,
				'visibleButtons' => [
					'delete' => function (PotentialClient $data): bool {
						return $data->isOwner(Yii::$app->user->getId());
					},
				],
			],
		],
	]); ?>


</div>
