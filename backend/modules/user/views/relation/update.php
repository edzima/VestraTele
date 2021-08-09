<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\user\UserRelation */

$this->title = Yii::t('backend', 'Update User Relation: {name}', [
	'name' => $model->user->getFullName(),
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'user_id' => $model->user_id, 'to_user_id' => $model->to_user_id, 'type' => $model->type]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-relation-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
