<?php

use common\models\user\UserVisible;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model UserVisible */
/* @var $users string[] */

$this->title = Yii::t('backend', 'Update User Visible: {user}', [
	'user' => $model->user->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Visibles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user->getFullName(), 'url' => ['user/view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');

?>
<div class="user-visible-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
		'users' => $users,
	]) ?>

</div>
