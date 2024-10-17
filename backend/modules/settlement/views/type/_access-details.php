<?php

use common\components\rbac\widget\ModelAccessDetailWidget;
use common\models\settlement\SettlementType;

/**
 * @var SettlementType $model
 */

?>

<?= ModelAccessDetailWidget::widget([
	'model' => $model,
]) ?>
