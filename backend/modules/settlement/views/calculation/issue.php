<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\widgets\IssueCreateSettlementButtonDropdown;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\modules\settlement\models\search\IssueToCreateCalculationSearch;
use backend\modules\settlement\widgets\IssuePayCalculationGrid;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $toCreateSearchModel IssueToCreateCalculationSearch */
/* @var $toCreateProvider ActiveDataProvider */
/* @var $searchModel IssuePayCalculationSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Calculations for: {issue}', ['issue' => $searchModel->issue->longId]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($searchModel->issue);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $searchModel->issue->longId;

?>
<div class="settlement-calculation-issue">

	<p>
		<?= IssueCreateSettlementButtonDropdown::widget([
			'issue' => $searchModel->issue,
			'userId' => Yii::$app->user->id,
		]) ?>
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
