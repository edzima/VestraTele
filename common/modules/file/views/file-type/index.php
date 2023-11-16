<?php

use common\modules\file\models\FileType;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\modules\file\models\search\FileTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('file', 'File Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'Files'), 'url' => ['file/index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-type-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('file', 'Create File Type'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
			'name',
			'is_active',
			'visibility',
			'validator_config:ntext',
			[
				'class' => ActionColumn::className(),
				'urlCreator' => function ($action, FileType $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
