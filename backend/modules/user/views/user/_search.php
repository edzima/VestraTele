<?php

use backend\modules\user\models\search\UserSearch;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>


	<div class="form-group row">


		<?= $form->field($model, 'role', ['options' => ['class' => 'col-md-3']])
			->widget(Select2::class, [
					'data' => UserSearch::getRolesNames(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('role'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<?= $form->field($model, 'permission', ['options' => ['class' => 'col-md-3']])
			->widget(Select2::class, [
					'data' => UserSearch::getPermissionsNames(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('permission'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<?= $form->field($model, 'trait', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => UserSearch::getUserTraitsNames(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('trait'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>


	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), 'index', ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
