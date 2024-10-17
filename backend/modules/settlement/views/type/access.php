<?php

use common\components\rbac\form\ModelActionsForm;
use common\components\rbac\widget\ModelAccessFormWidget;
use common\models\settlement\SettlementType;
use yii\web\View;

/**
 * @var View $this
 * @var ModelActionsForm $model
 * @var SettlementType $type
 */
$this->title = Yii::t('backend', 'Access to: {name}', [
	'name' => $type->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['calculation/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlement Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $type->name, 'url' => ['view', 'id' => $type->id]];

$this->params['breadcrumbs'][] = $this->title;
?>

<?= ModelAccessFormWidget::widget([
	'model' => $model,
]) ?>
