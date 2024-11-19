<?php

use common\components\rbac\form\ActionsAccessForm;
use common\components\rbac\widget\ActionsAccessFormWidget;
use common\models\settlement\CostType;
use yii\web\View;

/**
 * @var View $this
 * @var ActionsAccessForm $model
 * @var CostType $type
 */
$this->title = Yii::t('backend', 'Access to: {name}', [
	'name' => $type->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['cost/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Cost Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $type->name, 'url' => ['view', 'id' => $type->id]];

$this->params['breadcrumbs'][] = $this->title;
?>

<?= ActionsAccessFormWidget::widget([
	'model' => $model,
]) ?>
