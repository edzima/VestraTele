<?php

use backend\helpers\Html;
use backend\models\search\PotentialClientSearch;
use backend\widgets\GridView;
use common\helpers\Url;
use common\models\PotentialClient;
use common\widgets\grid\ActionColumn;
use kartik\select2\Select2;

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

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'firstname',
			'lastname',
			//	'details:ntext',
			'cityName',
			'phone:tel',
			'birthday',
			[
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => PotentialClientSearch::getStatusesNames(),
			],
			[
				'attribute' => 'owner_id',
				'filter' => PotentialClientSearch::getOwnersNames(),
				'value' => 'owner',
				'format' => 'userEmail',
				'label' => $searchModel->getAttributeLabel('owner'),
				'filterType' => GridView::FILTER_SELECT2,
				'filterInputOptions' => [
					'placeholder' => $searchModel->getAttributeLabel('owner'),
				],
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				],
			],
			'created_at:datetime',
			'updated_at:datetime',

			[
				'class' => ActionColumn::class,
				'urlCreator' => function ($action, PotentialClient $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
