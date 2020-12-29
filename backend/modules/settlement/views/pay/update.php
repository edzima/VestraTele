<?php

use backend\helpers\Breadcrumbs;
use common\models\issue\IssuePay;
use common\models\user\User;
use common\widgets\settlement\SettlementDetailView;

/* @var $this yii\web\View */
/* @var $model IssuePay */

$this->title = Yii::t('backend', 'Update pay: {value}', ['value' => Yii::$app->formatter->asCurrency($model->getValue())]);
$issue = $model->calculation->issue;
$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
	$this->params['breadcrumbs'][] = ['label' => $issue->longId, 'url' => ['/settlement/calculation/issue', 'id' => $issue->id]];
}
$this->params['breadcrumbs'][] = ['label' => $model->calculation->getTypeName(), 'url' => ['/settlement/calculation/view', 'id' => $model->calculation_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-calculation-update">

	<div class="row">
		<div class="col-md-5">
			<?= SettlementDetailView::widget([
				'model' => $model->calculation,
			]) ?>
		</div>
	</div>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
