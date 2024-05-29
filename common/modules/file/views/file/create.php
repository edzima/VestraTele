<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\file\models\File $model */

$this->title = Yii::t('file', 'Create File');
$this->params['breadcrumbs'][] = ['label' => Yii::t('file', 'Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
