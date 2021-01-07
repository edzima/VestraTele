<?php

use backend\helpers\Breadcrumbs;
use backend\modules\provision\models\SettlementProvisionsForm;
use common\widgets\settlement\SettlementDetailView;
use yii\web\View;

/* @var $this View */
/* @var $model SettlementProvisionsForm */

$this->title = Yii::t('backend', 'Set provisions for settlement: {type}', ['type' => $model->getModel()->getTypeName()]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->longId, 'url' => ['/settlement/calculation/issue', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getTypeName(), 'url' => ['/settlement/calculation/view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Set provisions for settlement');

?>
<div class="provision-settlement-set">

	<?= SettlementDetailView::widget([
		'model' => $model->getModel(),
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
