<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\file\models\FileAccess $model */

$this->title = Yii::t('file', 'Update File Access: {name}', [
	'name' => $model->file_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'File Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->file_id, 'url' => ['view', 'file_id' => $model->file_id, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = Yii::t('file', 'Update');
?>
<div class="file-access-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
