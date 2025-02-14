<?php

use common\modules\court\models\search\LawsuitSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LawsuitSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="court-hearing-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'court_type', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->dropDownList(LawsuitSearch::getCourtTypeNames(), [
			'prompt' => Yii::t('common', '--- Select ---'),
		]) ?>

		<?= $form->field($model, 'is_appeal', [
			'options' => [
				'class' => 'col-md-2 col-lg-2',
			],
		])->checkbox() ?>


	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('common', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
