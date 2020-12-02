<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\models\CalculationProblemStatusForm;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model CalculationProblemStatusForm */

$this->title = Yii::t('backend', 'Set problem status for calculation: {id}', ['id' => $model->getModel()->id]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getModel()->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->id, 'url' => ['view', $model->getModel()->id]];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-calculation-problem-status">
	<div class="row">
		<div class="col-md-5">
			<?= DetailView::widget([
				'model' => $model->getModel()->issue,
				'attributes' => [
					'customer.fullName:text:' . $model->getModel()->issue->getAttributeLabel('customer'),
					'type',
					'stage',
				],
			]) ?>

		</div>
	</div>

	<?= $this->render('_problem-status_form', [
		'model' => $model,
	]) ?>

</div>
