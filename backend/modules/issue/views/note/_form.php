<?php

use backend\modules\issue\models\IssueNoteForm;
use common\modules\issue\widgets\IssueNoteFormWidget;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $form yii\widgets\ActiveForm */
?>

<?= IssueNoteFormWidget::widget([
	'model' => $model,
]) ?>


