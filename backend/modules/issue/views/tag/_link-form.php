<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueTagsLinkForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model IssueTagsLinkForm */

?>

<div class="issue-tag-link-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $model->scenario === IssueTagsLinkForm::SCENARIO_MULTIPLE_ISSUES
		? Html::hiddenInput('issuesIds', implode(',', $model->issuesIds))
		: ''
	?>

	<div class="row">

		<?php foreach ($model->getTypes() as $id => $name): ?>

			<?= $form->field($model, "typeTags[$id]", [
				'options' => [
					'class' => 'col-md-4 col-lg-3',
				],
			])->widget(Select2::class, [
				'data' => $model->getTagsNames($id),
				'options' => [
					'multiple' => true,
				],
				'pluginOptions' => [
					'tags' => true,
				],
			])->label($name)
			?>


		<?php endforeach; ?>


		<?= $form->field($model, 'withoutType', [
			'options' => [
				'class' => 'col-md-4 col-lg-3',
			],
		])->widget(Select2::class, [
			'data' => $model->getTagsNames(null),
			'options' => [
				'multiple' => true,
			],
			'pluginOptions' => [
				'tags' => true,
			],
		])
		?>

	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
