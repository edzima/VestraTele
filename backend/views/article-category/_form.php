<?php

use common\helpers\Url;
use vova07\imperavi\Widget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCategory */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $categories string[] */
?>

<div class="article-category-form">

	<?php $form = ActiveForm::begin() ?>

	<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'comment')->widget(Widget::class, [
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

	<?= $form->field($model, 'parent_id')->dropDownList($categories, ['prompt' => '']) ?>

	<?= $form->field($model, 'status')->checkbox(['label' => Yii::t('backend', 'Activate')]) ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
