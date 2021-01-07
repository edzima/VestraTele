<?php

use common\models\settlement\PayReceived;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PayReceived */

$this->title = Yii::t('settlement', 'Update Pay Received: {name}', [
	'name' => $model->pay_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Pays'), 'url' => ['/settlement/pay/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Received pays'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="pay-received-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
