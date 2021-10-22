<?php

use common\models\message\IssueMessagesForm;
use common\widgets\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model IssueMessagesForm */
/* @var $checkboxesAttributes string[] */
?>

<div class="issue-messages-form">
	<?php foreach ($checkboxesAttributes as $attribute): ?>
		<?= $form->field($model, $attribute)->checkbox() ?>
	<?php endforeach; ?>
</div>
