<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\models\CalculationProblemStatusForm;
use common\models\user\User;
use common\widgets\settlement\SettlementDetailView;
use yii\web\View;

/* @var $this View */
/* @var $model CalculationProblemStatusForm */

$this->title = Yii::t('backend', 'Set uncollectible status');

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getModel()->issue);
if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Settlements'), 'url' => ['/settlement/calculation/index']];
}
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getTypeName(), 'url' => ['/settlement/calculation/view', $model->getModel()->id]];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-calculation-problem-status">
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
