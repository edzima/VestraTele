<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\settlement\SettlementType $model */

$this->title = Yii::t('settlement', 'Create Settlement Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['calculation/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlement Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-type-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
