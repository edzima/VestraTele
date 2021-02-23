<?php

use backend\modules\provision\models\ProvisionTypeForm;

/* @var $this yii\web\View */
/* @var $model ProvisioNTypeForm */

$this->title = Yii::t('provision', 'Update provision type: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="provision-type-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
