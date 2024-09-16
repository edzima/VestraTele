<?php

use backend\modules\settlement\models\SettlementTypeForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var SettlementTypeForm $model */

$this->title = Yii::t('settlement', 'Update Settlement Type: {name}', [
	'name' => $model->getModel()->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['calculation/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlement Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('settlement', 'Update');
?>
<div class="settlement-type-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
