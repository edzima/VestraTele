<?php

use backend\modules\settlement\models\search\IssueCostSearch;
use backend\modules\settlement\widgets\IssueCostActionColumn;
use backend\widgets\GridView;
use common\models\issue\Issue;
use common\models\user\Worker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueCostSearch */
/* @var $issue Issue */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Costs: {issue}', ['issue' => $issue->longId]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $issue->longId, 'url' => ['/issue/issue/view', 'id' => $issue->id]];
$this->params['breadcrumbs'][] = Yii::t('settlement', 'Costs');
?>
<div class="issue-cost-issue">

	<p>
		<?= Html::a(Yii::t('backend', 'Create'), ['create', 'id' => $issue->id], ['class' => 'btn btn-success']) ?>
		<?= Yii::$app->user->can(Worker::PERMISSION_COST_DEBT)
			? Html::a(Yii::t('settlement', 'Create Debt Costs'), ['create-debt', 'issue_id' => $issue->id], ['class' => 'btn btn-primary'])
			: ''
		?>
	</p>


	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $model,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => IssueCostSearch::getTypesNames(),
			],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => IssueCostSearch::getUsersNames(),
			],
			[
				'attribute' => 'value',
				'value' => 'valueWithVAT',
				'format' => 'currency',
				'label' => Yii::t('backend', 'Value with VAT'),
			],
			'valueWithoutVAT:currency:' . Yii::t('backend', 'Value without VAT'),
			'VATPercent',
			[
				'attribute' => 'transfer_type',
				'value' => 'transferTypeName',
				'filter' => IssueCostSearch::getTransfersTypesNames(),
			],
			'date_at:date',
			'settled_at:date',
			'created_at:datetime',
			'updated_at:datetime',
			[
				'class' => IssueCostActionColumn::class,
			],
		],
	]); ?>


</div>
