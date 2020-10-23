<?php

use backend\modules\user\models\CustomerUserForm;

/* @var $this yii\web\View */
/* @var $model CustomerUserForm */

$this->title = Yii::t('backend', 'Update customer: {username}', ['username' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getFullName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');

?>
<div class="customer-update">

	<?= $this->render('_form', [
			'model' => $model,
		]
	) ?>

</div>
