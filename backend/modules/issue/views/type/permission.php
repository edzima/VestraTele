<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueTypePermissionForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model IssueTypePermissionForm */

$this->title = Yii::t('issue', 'Issue Type - Permissions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Types'), 'url' => ['index']];
if ($model->getModel()) {
	$this->title = Yii::t('issue', 'Issue Type: {name} -  Permissions', [
		'name' => $model->getModel()->name,
	]);
	$this->params['breadcrumbs'][] = ['label' => $model->getModel()->name, 'url' => ['view', 'id' => $model->getModel()->id]];
}

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-type-permission">
	<div class="row">
		<div class="col-md-6">
			<?php $form = ActiveForm::begin(); ?>


			<?= $form->field($model, 'roles')->widget(Select2::class, [
				'data' => $model->getRolesNames(),
				'options' => [
					'multiple' => true,
				],
			]) ?>

			<?= $form->field($model, 'permissions')->widget(Select2::class, [
				'data' => $model->getPermissionsNames(),
				'options' => [
					'multiple' => true,
				],
			]) ?>

			<div class="form-group">
				<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>


</div>
