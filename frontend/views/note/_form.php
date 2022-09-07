<?php

use common\modules\issue\widgets\IssueNoteFormWidget;
use frontend\models\IssueNoteForm;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?= IssueNoteFormWidget::widget([
	'model' => $model,
]) ?>
