<?php

use backend\helpers\Breadcrumbs;
use backend\modules\provision\models\SettlementProvisionsForm;
use common\widgets\settlement\SettlementDetailView;
use yii\web\View;

/* @var $this View */
/* @var $model SettlementProvisionsForm */

$this->title = Yii::t('backend', 'Set provisions for settlement: {type}', ['type' => $model->getModel()->getTypeName()]);
$this->params['breadcrumbs'] = array_merge(
	Breadcrumbs::issue($model->getModel()),
	Breadcrumbs::settlement($model->getModel())
);

$this->params['breadcrumbs'][] = Yii::t('backend', 'Set provisions for settlement');

?>
<div class="provision-settlement-set">

	<?= SettlementDetailView::widget([
		'model' => $model->getModel(),
		'withValueWithoutCosts' => true,
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
