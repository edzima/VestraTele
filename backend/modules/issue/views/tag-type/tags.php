<?php

use backend\modules\issue\models\TypeTagsForm;
use common\helpers\Html;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model TypeTagsForm */

$this->title = Yii::t('backend', 'Type: {type} Tags', [
	'type' => $model->getType()->name,
]);
$this->params['breadcrumbs'][] = ['url' => ['issue/index'], 'label' => Yii::t('issue', 'Issues')];
$this->params['breadcrumbs'][] = ['url' => ['tag/index'], 'label' => Yii::t('issue', 'Tags')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Tag Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getType()->name, 'url' => ['view', 'id' => $model->getType()->id]];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-tag-type-tags">


	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'tags')->widget(Select2::class, [
		'data' => TypeTagsForm::getTagsNames(false),
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
