<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\settlement\models\CalculationForm;
use common\models\user\User;
use common\widgets\settlement\SettlementDetailView;

/* @var $this yii\web\View */
/* @var $model CalculationForm */

$this->title = Yii::t('backend', 'Update settlement: {type}', ['type' => $model->getModel()->getTypeName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->getIssue()->getIssueName(), 'url' => ['issue', 'id' => $model->getIssue()->getIssueId()]];
}
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getTypeName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-pay-calculation-update">

	<p>
		<?= Yii::$app->user->can(User::PERMISSION_COST)
			? Html::a(
				Yii::t('backend', 'Create cost'),
				['/settlement/cost/create', 'id' => $model->getModel()->getIssueId()],
				[
					'class' => 'btn btn-warning',
				]
			)
			: ''
		?>
	</p>

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
