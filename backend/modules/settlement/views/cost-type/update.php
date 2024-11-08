<?php

use backend\modules\settlement\models\CostTypeForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var CostTypeForm $model */

$this->title = Yii::t('backend', 'Update Cost Type: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['url' => ['cost/index'], 'label' => Yii::t('settlement', 'Costs')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Cost Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="cost-type-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
