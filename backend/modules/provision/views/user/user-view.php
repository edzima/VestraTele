<?php

use backend\helpers\Html;
use backend\modules\provision\widgets\UserProvisionsWidget;
use common\models\provision\ProvisionUserData;

/* @var $this yii\web\View */
/* @var $model ProvisionUserData */

$this->title = $model->hasType()
	? Yii::t('provision', 'Schemas provisions: {type} for {user}', [
		'type' => $model->type->name, 'user' => $model->getUser()->getFullName(),
	])
	: Yii::t('provision', 'Schemas provisions: {user}', ['user' => $model->getUser()->getFullName()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['provision/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions types'), 'url' => ['type/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Schemas provisions'), 'url' => ['index']];
if ($model->hasType()) {
	$this->params['breadcrumbs'][] = ['label' => $model->getUser()->getFullName(), 'url' => ['user-view', 'userId' => $model->getUser()->id]];
	$this->params['breadcrumbs'][] = $model->type->name;
} else {
	$this->params['breadcrumbs'][] = $model->getUser()->getFullName();
}

?>
<div class="provision-user-type">

	<p>
		<?= Html::a(
			Yii::t('backend', 'Assign supervisor'),
			['/user/worker/hierarchy', 'id' => $model->getUser()->id],
			['class' => 'btn btn-success'])
		?>
	</p>

	<?= UserProvisionsWidget::widget([
		'userData' => $model,
	]) ?>


</div>
