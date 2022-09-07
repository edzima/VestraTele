<?php

use common\modules\issue\widgets\SummonFormWidget;
use frontend\models\SummonForm;

/* @var $this yii\web\View */
/* @var $model SummonForm */

?>

<?= SummonFormWidget::widget([
	'model' => $model,
]) ?>
