<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\modules\court\models\Court $model */

$this->title = Yii::t('court', 'Update Court: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('court', 'Courts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('court', 'Update');
?>
<div class="court-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
