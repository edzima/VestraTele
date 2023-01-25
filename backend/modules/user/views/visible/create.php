<?php

use common\models\user\User;
use common\models\user\UserVisible;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model UserVisible */
/* @var $user User|null */
/* @var $users string[] */

if ($user) {
	$this->title = Yii::t('backend', 'Create User Visible: {user}', [
		'user' => $user->getFullName(),
	]);
} else {
	$this->title = Yii::t('backend', 'Create User Visible');
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Visibles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-visible-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
		'users' => $users,
		'withUser' => $user === null,
	]) ?>

</div>
