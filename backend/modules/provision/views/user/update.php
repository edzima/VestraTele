<?php

use backend\modules\provision\models\ProvisionUserForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUserForm */

$this->title = Yii::t('provision', 'Update schema provision: {name}', ['name' => $model->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Schemas provisions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="provision-user-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
