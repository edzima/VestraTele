<?php

/* @var $this yii\web\View */
/* @var $model common\models\user\UserTrait */

$this->title = Yii::t('backend', 'Create User Trait');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['customer/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Traits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-trait-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
