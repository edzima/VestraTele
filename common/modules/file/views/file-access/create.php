<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\file\models\FileAccess $model */

$this->title = Yii::t('file', 'Create File Access');
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'File Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-access-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
