<?php

use common\widgets\settlement\SettlementDetailView;
use frontend\helpers\Html;
use frontend\models\UpdatePayForm;

/* @var $this yii\web\View */
/* @var $model UpdatePayForm */

$this->title = Yii::t('settlement', 'Update pay: {value}', [
	'value' => Yii::$app->formatter->asCurrency($model->getPay()->getValue()),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['settlement/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getPay()->calculation->getTypeName(), 'url' => ['settlement/view', 'id' => $model->getPay()->calculation_id]];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-pay-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= SettlementDetailView::widget(['model' => $model->getPay()->calculation]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
