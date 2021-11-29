<?php

use backend\helpers\Breadcrumbs;
use common\models\settlement\PayPayedForm;
use common\models\user\User;
use common\widgets\settlement\SettlementDetailView;

/* @var $this yii\web\View */
/* @var $model PayPayedForm */

$this->title = Yii::t('settlement', 'Payed pay({partInfo}): {value}', [
	'value' => Yii::$app->formatter->asCurrency($model->getPay()->getValue()),
	'partInfo' => $model->getPay()->getPartInfo(),
]);
$calculation = $model->getPay()->calculation;
$this->params['breadcrumbs'] = Breadcrumbs::issue($calculation);
if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['/settlement/calculation/index']];
	$this->params['breadcrumbs'][] = ['label' => $calculation->getIssueName(), 'url' => ['/settlement/calculation/issue', 'id' => $calculation->getIssueId()]];
}
$this->params['breadcrumbs'][] = ['label' => $calculation->getTypeName(), 'url' => ['/settlement/calculation/view', 'id' => $calculation->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-pay">

	<div class="row">
		<div class="col-md-5">
			<?= SettlementDetailView::widget([
				'model' => $calculation,
			]) ?>
		</div>
	</div>

	<?= $this->render('_payed_form', [
		'model' => $model,
	]) ?>

</div>
