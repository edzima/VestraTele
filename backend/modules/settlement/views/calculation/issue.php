<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $toCreateSearchModel IssueToCreateCalculationSearch */
/* @var $toCreateProvider ActiveDataProvider */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Calculations for: {issue}', ['issue' => $searchModel->issue->longId]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($searchModel->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $searchModel->issue, 'url' => ['issue', 'id' => $searchModel->issue_id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settlement-calculation-issue">

	<p>
		<?= Html::a(Yii::t('settlement', 'Create settlement'), ['create', 'id' => $searchModel->issue_id], ['class' => 'btn btn-success']) ?>
	</p>

	<?php if ($toCreateProvider->getTotalCount() > 0): ?>
		<fieldset>
			<legend><?= Yii::t('backend', 'To create') ?></legend>

			<?= $this->render('_to-create_grid', [
				'searchModel' => $toCreateSearchModel,
				'dataProvider' => $toCreateProvider,
				'withIssue' => false,
				'withCustomer' => false,
			]) ?>

		</fieldset>
	<?php endif; ?>

	<fieldset>
		<legend><?= Yii::t('backend', 'Issue calculations') ?></legend>

		<?= IssuePayCalculationGrid::widget([
			'filterModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'withIssue' => false,
			'withCustomer' => false,
		]) ?>

	</fieldset>

</div>
