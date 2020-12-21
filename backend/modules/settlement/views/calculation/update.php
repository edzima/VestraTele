<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\models\CalculationForm;
use common\models\user\User;
use common\widgets\settlement\SettlementDetailView;

/* @var $this yii\web\View */
/* @var $model CalculationForm */

$this->title = Yii::t('backend', 'Update settlement: {type}', ['type' => $model->getModel()->getTypeName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->longId, 'url' => ['issue', 'id' => $model->getIssue()->id]];
}
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getTypeName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-pay-calculation-update">

	<div class="row">
		<div class="col-md-5">
			<?= SettlementDetailView::widget([
				'model' => $model->getModel(),
			]) ?>
		</div>
	</div>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
