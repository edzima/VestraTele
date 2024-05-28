<?php

use common\modules\file\models\FileTypeForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var FileTypeForm $model */

$this->title = Yii::t('file', 'Create File Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'Files'), 'url' => ['file/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'File Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-type-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
