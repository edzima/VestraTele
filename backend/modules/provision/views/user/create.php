<?php

use backend\modules\provision\models\ProvisionUserForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUserForm */

$this->title = Yii::t('provision', 'Create provision schema');
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions types'), 'url' => ['/type/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Schemas provisions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('common', 'Create');
?>
<div class="provision-user-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
