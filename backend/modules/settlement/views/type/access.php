<?php

use common\components\rbac\form\ModelActionsForm;
use common\components\rbac\widget\ModelAccessFormWidget;
use yii\web\View;

/**
 * @var View $this
 * @var ModelActionsForm $model
 */

?>

<?= ModelAccessFormWidget::widget([
	'model' => $model,
]) ?>
