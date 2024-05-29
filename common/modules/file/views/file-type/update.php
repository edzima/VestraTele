<?php

use common\modules\file\models\FileTypeForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var FileTypeForm $model */

$this->title = Yii::t('file', 'Update File Type: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'File Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('file', 'Update');
?>
<div class="file-type-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
