<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\issue\models\IssueTagsLinkForm;
use common\models\user\Worker;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model IssueTagsLinkForm */

$this->title = Yii::t('issue', 'Issue: {issue} - Tags', [
	'issue' => $model->getIssue()->getIssueName(),
]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_TAG_MANAGER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Tags'), 'url' => ['index']];
}
?>
<div class="issue-tag-issue-tags-link">
	<?php $form = ActiveForm::begin(); ?>

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
