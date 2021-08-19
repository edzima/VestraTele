<?php

use backend\helpers\Breadcrumbs;
use backend\modules\settlement\widgets\IssuePayGrid;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\modules\issue\widgets\IssueNotesWidget;
use common\widgets\grid\CurrencyColumn;
use common\widgets\GridView;
use common\widgets\settlement\SettlementDetailView;
use Decimal\Decimal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model IssuePayCalculation */

$this->title = Yii::t('settlement', 'Settlement {type}', ['type' => $model->getTypeName()]);

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->issue);
if (Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->issue, 'url' => ['issue', 'id' => $model->issue_id]];
}
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

?>
<div class="issue-pay-calculation-view">

	<p>
		<?= Yii::$app->user->can(User::PERMISSION_NOTE)
			? Html::a(Yii::t('backend', 'Create note'), ['/issue/note/create-settlement', 'id' => $model->id], ['class' => 'btn btn-info'])
			: ''
		?>

		<?= $model->owner_id === Yii::$app->user->getId()
		|| Yii::$app->user->can(User::ROLE_BOOKKEEPER)
			? Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>

		<?= !$model->isPayed() && Yii::$app->user->can(User::PERMISSION_CALCULATION_PAYS)
			? Html::a(Yii::t('backend', 'Generate pays'), ['pays', 'id' => $model->id], ['class' => 'btn btn-primary'])
			: ''
		?>

		<?= !$model->isPayed() && Yii::$app->user->can(User::PERMISSION_CALCULATION_PROBLEMS)
			? Html::a(Yii::t('backend', 'Set uncollectible status'), ['/settlement/calculation-problem/set', 'id' => $model->id], ['class' => 'btn btn-warning'])
			: ''
		?>

		<?= Yii::$app->user->can(User::ROLE_ADMINISTRATOR) && $model->hasPays()
			? Html::a(Yii::t('backend', 'Provisions'), ['/provision/settlement/view', 'id' => $model->id], ['class' => 'btn btn-success'])
			: ''
		?>


		<?= Yii::$app->user->can(User::ROLE_BOOKKEEPER) ? Html::a('Usuń', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger pull-right',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) : '' ?>
	</p>

	<?= SettlementDetailView::widget([
		'model' => $model,
	]) ?>

	<?= IssuePayGrid::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getPays(),
		]),
		'visibleAgent' => false,
		'visibleCustomer' => false,
		'caption' => Yii::t('settlement', 'Pays'),
		'summary' => '',
		'visibleProvisionsDetails' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR) || $model->issue->isForUser(Yii::$app->user->getId()),
		'visibleSettlementType' => false,
	])
	?>

	<?= GridView::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $model->getCosts()->joinWith('user.userProfile'),
		]),
		'summary' => '',
		'showPageSummary' => true,
		'caption' => Yii::t('settlement', 'Costs'),
		'columns' => [
			'typeName',
			'user',
			'vatPercent:text:' . Yii::t('settlement', 'VAT (%)'),
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'valueWithoutVAT',
				'pageSummary' => true,
				'pageSummaryFunc' => static function (array $decimals): Decimal {
					$sum = new Decimal(0);
					foreach ($decimals as $decimal) {
						$sum = $sum->add($decimal);
					}
					return $sum;
				},
			],
			[
				'class' => CurrencyColumn::class,
				'attribute' => 'valueWithVAT',
				'pageSummary' => true,
				'pageSummaryFunc' => static function (array $decimals): Decimal {
					$sum = new Decimal(0);
					foreach ($decimals as $decimal) {
						$sum = $sum->add($decimal);
					}
					return $sum;
				},
			],
		],
	]) ?>

	<?= IssueNotesWidget::widget([
		'model' => $model->issue,
		'notes' => $model->issue->getIssueNotes()->onlySettlement($model->id)->all(),
		'type' => IssueNotesWidget::TYPE_SETTLEMENT,
	])
	?>

</div>
