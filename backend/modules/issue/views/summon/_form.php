<?php

use backend\modules\issue\models\SummonForm;
use common\modules\issue\widgets\SummonFormWidget;

/* @var $this yii\web\View */
/* @var $model SummonForm */
/* @var $form yii\widgets\ActiveForm */

?>

<?= SummonFormWidget::widget([
	'model' => $model,
]) ?>
