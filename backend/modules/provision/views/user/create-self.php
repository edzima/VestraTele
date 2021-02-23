<?php

use backend\modules\provision\models\ProvisionUserForm;
use common\models\user\User;
use yii\web\View;

/** @var $this View */
/** @var User $user */
/** @var ProvisionUserForm $model */

$this->title = Yii::t('provision', 'Create self provision: {user}', ['user' => $user]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Schemas provisions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->getFullName(), 'url' => ['user-view', 'userId' => $user->id, 'typeId' => $model->type_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Create');
?>

<div class="provision-user-create-self">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
