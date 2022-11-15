<?php

use common\models\PotentialClient;
use common\widgets\grid\ActionColumn;
use frontend\helpers\Html;
use frontend\models\search\PotentialClientSearch;
use frontend\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel PotentialClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider|null */

$this->title = Yii::t('common', 'Potential Clients - Search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="potential-client-search">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>

		<?= Html::a(Yii::t('common', 'Self'), ['self'], ['class' => 'btn btn-warning']) ?>

		<?= Html::a(Yii::t('common', 'Create Potential Client'), ['create'], ['class' => 'btn btn-success']) ?>

	</p>

	<?= $this->render('_search', [
		'model' => $searchModel,
		'withAddress' => false,
		'withFirstname' => true,
		'withLastname' => true,
		'action' => 'search',
	]); ?>

	<?= $dataProvider !== null
		? GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'columns' => [
				['class' => 'yii\grid\SerialColumn'],
				[
					'attribute' => 'status',
					'value' => 'statusName',
					'filter' => PotentialClientSearch::getStatusesNames(),
				],
				'firstname',
				'lastname',
				'birthday',
				'created_at:date',
				'updated_at:date',

				[
					'class' => ActionColumn::class,
					'visibleButtons' => [
						'delete' => function (PotentialClient $data): bool {
							return $data->isOwner(Yii::$app->user->getId());
						},
						'update' => function (PotentialClient $data): bool {
							return $data->isOwner(Yii::$app->user->getId());
						},
						'view' => function (PotentialClient $data): bool {
							return $data->isOwner(Yii::$app->user->getId());
						},

					],
				],
			],
		])
		: ''
	?>


</div>
