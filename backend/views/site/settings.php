<?php

use common\components\keyStorage\FormModel;
use common\components\keyStorage\FormWidget;
use common\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model FormModel */
/* @var $form ActiveForm */

$this->title = Yii::t('backend', 'Application settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-settings">

	<?= FormWidget::widget([
		'model' => $model,
		'submitText' => Yii::t('backend', 'Save'),
		'submitOptions' => ['class' => 'btn btn-primary'],
	]) ?>

</div>
