<?php

use common\helpers\Html;
use common\helpers\Url;
use common\modules\file\models\FileAccess;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;

/** @var yii\web\View $this */
/** @var common\modules\file\models\search\FileAccessSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('file', 'File Accesses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-access-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'file_id',
			'user_id',
			'access',
			[
				'class' => ActionColumn::class,
				'urlCreator' => function ($action, FileAccess $model, $key, $index, $column) {
					return Url::toRoute([$action, 'file_id' => $model->file_id, 'user_id' => $model->user_id]);
				},
			],
		],
	]); ?>


</div>
