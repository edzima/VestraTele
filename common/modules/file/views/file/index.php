<?php

use common\modules\file\models\File;
use common\modules\file\models\search\FileSearch;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var FileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('file', 'Files');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('file', 'File Types'), ['file-type/index'], ['class' => 'btn btn-info']) ?>
		<?= Html::a(Yii::t('file', 'Access'), ['file-access/index'], ['class' => 'btn btn-warning']) ?>

	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			//	'id',
			[
				'attribute' => 'file_type_id',
				'value' => function (File $file): string {
					return Html::a(Html::encode($file->fileType->name), ['file-type/view', 'id' => $file->file_type_id]);
				},
				'filter' => FileSearch::getFileTypesNames(),
				'format' => 'html',
			],
			'name',
			[
				'attribute' => 'type',
				'filter' => FileSearch::getTypesNames(),
			],
			//'hash',
			[
				'attribute' => 'size',
				'value' => 'formattedSize',
			],

			//'mime',
			'created_at:datetime',
			'updated_at:datetime',
			//'owner_id',
			[
				'class' => ActionColumn::class,
				'urlCreator' => function ($action, File $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
