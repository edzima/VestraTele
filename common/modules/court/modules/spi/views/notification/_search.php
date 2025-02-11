<?php

use common\modules\court\modules\spi\entity\search\NotificationSearch;
use common\modules\court\modules\spi\Module;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var NotificationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="spi-notification-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index', 'appeal' => $model->getAppeal()],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'fromAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'toAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Module::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Module::t('common', 'Reset'), ['index', 'appeal' => $model->getAppeal()], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
