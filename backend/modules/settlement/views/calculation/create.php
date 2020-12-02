<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Url;
use backend\modules\settlement\models\CalculationForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model CalculationForm */

$this->title = Yii::t('backend', 'Create calculation for: {issue}', ['issue' => $model->getIssue()->longId]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue(), 'url' => ['issue', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-calculation-create">


	<h2> <?= Html::a(
			$model->getIssue(),
			Url::issueView($model->getIssue()->id),
			['target' => '_blank']) ?>
	</h2>
	<div class="row">
		<div class="col-md-5">
			<?= DetailView::widget([
				'model' => $model->getIssue(),
				'attributes' => [
					'customer.fullName:text:' . $model->getIssue()->getAttributeLabel('customer'),
					'type',
					'stage',
				],
			]) ?>

		</div>
	</div>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>


</div>
