<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\models\ArticleForm;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use vova07\imperavi\Widget;
use yii\web\View;

/* @var $this View */
/* @var $model ArticleForm */
/* @var $form ActiveForm */
?>

<div class="article-form">

	<?php $form = ActiveForm::begin(['id' => 'article-form']) ?>

	<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'preview')->widget(Widget::class, [
		'settings' => [
			'minHeight' => 200,
			'plugins' => [
				'filemanager',
				'fullscreen',
				'fontcolor',
				'imagemanager',
				'table',
				'video',
			],
			'imageUpload' => Url::to(['/site/image-upload']),
			'fileUpload' => Url::to(['/site/file-upload']),
		],
	]) ?>

	<?= $form->field($model, 'body')->widget(Widget::class, [
		'settings' => [
			'minHeight' => 200,
			'plugins' => [
				'filemanager',
				'fullscreen',
				'fontcolor',
				'imagemanager',
				'table',
				'video',
			],
			'convertDivs' => false,
			'replaceDivs' => false,
			'removeEmptyTags' => true,
			'imageUpload' => Url::to(['/site/image-upload']),
			'fileUpload' => Url::to(['/site/file-upload']),
		],
	]) ?>

	<?= $form->field($model, 'status')->checkbox(['label' => Yii::t('backend', 'Activate')]) ?>

	<?= $form->field($model, 'show_on_mainpage')->textInput()->hint(Yii::t('common', 'Empty: not visible. Number for Order.')) ?>


	<?= $form->field($model, 'category_id')->dropDownList(
		$model->getCategoriesNames(),
		['prompt' => Yii::t('common', 'Select...')])
	?>


	<?= $form->field($model, 'published_at')->widget(DateTimeWidget::class) ?>

	<?= $form->field($model, 'usersIds')->widget(
		Select2::class, [
			'data' => $model->getUsersNames(),
			'options' => [
				'multiple' => true,
				'prompt' => $model->getAttributeLabel('usersIds'),
			],
		]
	)->hint(Yii::t('backend', 'Empty: all')) ?>

	<div class="form-group">
		<?= Html::submitButton($model->getModel()->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->getModel()->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
