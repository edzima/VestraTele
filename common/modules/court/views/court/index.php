<?php

use common\modules\court\models\Court;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\modules\court\models\search\CourtSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('court', 'Courts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="court-index">

	<h1><?= Html::encode($this->title) ?></h1>


	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'name',
				'noWrap' => true,
			],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => Court::getTypesNames(),
			],
			'phone:tel',
			//'fax',
			'email:email',
			//'updated_at',
			[
				'attribute' => 'parent_id',
				'noWrap' => true,
				'value' => function (Court $data): ?string {
					$parent = $data->parent;
					if ($parent === null) {
						return null;
					}
					return Html::a(Html::encode($parent->name), [
						'view', 'id' => $parent->id,
					]);
				},
				'format' => 'html',
			],
			'spi_appeal',
			'SPIAppealWithParents',
			[
				'class' => ActionColumn::class,
				'template' => '{view} {update}',
				'urlCreator' => function ($action, Court $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
