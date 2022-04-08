<?php

use backend\modules\user\models\search\CustomerUserSearch;
use backend\modules\user\models\search\UserSearch;
use common\helpers\Html;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model CustomerUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>





	<?= $form->field($model, 'trait')
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


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), 'index', ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
