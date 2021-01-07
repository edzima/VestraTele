<?php

/* @var $this yii\web\View */
/* @var $model common\models\settlement\PayReceived */

$this->title = Yii::t('settlement', 'Receive pays');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Pays'), 'url' => ['/settlement/pay/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Received pays'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-received-receive">

	<?= $this->render('_receive_form', [
		'model' => $model,
	]) ?>

</div>
