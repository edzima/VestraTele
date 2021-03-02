<?php

use common\widgets\DateTimeWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use vova07\imperavi\Widget;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $categories string[] */
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
			'imageUpload' => Url::to(['/site/image-upload']),
			'fileUpload' => Url::to(['/site/file-upload']),
		],
	]) ?>

	<?= $form->field($model, 'status')->checkbox(['label' => Yii::t('backend', 'Activate')]) ?>

	<?= $form->field($model, 'category_id', ['options' => ['class' => 'col-md-8']])->dropDownList(ArrayHelper::map(
		$categories,
		'id',
		'title'
	), ['prompt' => '']
	) ?>


	<?= $form->field($model, 'published_at')->widget(
		DateTimeWidget::class,
		[
			'clientOptions' => [

				'allowInputToggle' => true,
				'sideBySide' => true,
				'widgetPositioning' => [
					'horizontal' => 'auto',
					'vertical' => 'auto',
				],
			],
		]
	) ?>


	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
