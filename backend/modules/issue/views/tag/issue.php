<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\issue\models\IssueTagsLinkForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model IssueTagsLinkForm */

$this->title = Yii::t('issue', 'Issue: {issue} - Tags', [
	'issue' => $model->getIssue()->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Tags'), 'url' => ['index']];
?>
<div class="issue-tag-issue-tags-link">
	<?php $form = ActiveForm::begin(); ?>

	<?php foreach ($model->getTypes() as $id => $name): ?>

		<?= !empty($model->getTagsNames($id))
			? $form->field($model, "typeTags[$id]")->widget(Select2::class, [
				'data' => $model->getTagsNames($id),
				'options' => [
					'multiple' => true,
				],
				'pluginOptions' => [
					'tags' => true,
				],
			])->label($name)
			: ''
		?>


	<?php endforeach; ?>


	<?= $form->field($model, 'withoutType')->widget(Select2::class, [
		'data' => $model->getTagsNames(null),
		'options' => [
			'multiple' => true,
		],
		'pluginOptions' => [
			'tags' => true,
		],
	])
	?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
