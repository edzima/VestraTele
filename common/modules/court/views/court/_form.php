<?php

use common\helpers\ArrayHelper;
use common\modules\court\models\Court;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Court $model */
/** @var yii\widgets\ActiveForm $form */
var_dump($model->getErrors());
?>

<div class="court-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'type')->dropDownList(Court::getTypesNames()) ?>

	<?= $form->field($model, 'parent_id')->widget(Select2::class, [
		'data' => ArrayHelper::map(Court::find()->andFilterWhere(['!=', 'id', $model->id])->all(), 'id', 'name'),
		'pluginOptions' => [
			'placeholder' => $model->getAttributeLabel('parent_id'),
		],
	]) ?>


	<?= $form->field($model, 'phone')->textarea(['rows' => 2]) ?>

	<?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('court', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
