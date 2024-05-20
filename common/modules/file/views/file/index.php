<?php

use common\modules\file\models\File;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\modules\file\models\search\FileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('file', 'Files');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?php
		//Html::a(Yii::t('file', 'Create File'), ['create'], ['class' => 'btn btn-success'])
		?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'name',
			'hash',
			'size',
			'type',
			//'mime',
			//'file_type_id',
			//'created_at',
			//'updated_at',
			//'owner_id',
			[
				'class' => ActionColumn::className(),
				'urlCreator' => function ($action, File $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
