<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\settlement\models\IssueCostForm;

/* @var $this yii\web\View */
/* @var $model IssueCostForm */

$this->title = Yii::t('settlement', 'Settle cost: {cost}', ['cost' => $model->getModel()->getTypeNameWithValue()]);
if ($model->getIssue()) {
	$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->id, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('settlement', 'Settle');
?>
<div class="issue-cost-settle">
	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->getModel()->id], ['class' => 'btn btn-primary']) ?>
	</p>
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
