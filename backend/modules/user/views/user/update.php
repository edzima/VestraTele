<?php

use backend\modules\user\models\UserForm;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserForm */
/* @var $form ActiveForm */

$this->title = Yii::t('backend', 'Update user: {username}', ['username' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getFullName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-update">

	<?= $this->render('_form', [
			'model' => $model,
		]
	) ?>

</div>
