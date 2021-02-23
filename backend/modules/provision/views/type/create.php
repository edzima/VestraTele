<?php

use backend\modules\provision\models\ProvisionTypeForm;

/* @var $this yii\web\View */
/* @var $model ProvisionTypeForm */

$this->title = Yii::t('provision', 'Create provision type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Create');
?>
<div class="provision-type-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
