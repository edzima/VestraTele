<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\provision\models\SettlementUserProvisionsForm;
use backend\modules\provision\widgets\UserProvisionsWidget;
use backend\modules\settlement\widgets\IssueCostActionColumn;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\provision\ProvisionUser;
use common\widgets\grid\CurrencyColumn;
use common\widgets\settlement\SettlementDetailView;
use Decimal\Decimal;
use yii\bootstrap\Nav;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $model SettlementUserProvisionsForm */
/* @var $issueCostDataProvider ActiveDataProvider */
/* @var $userNotSettledCosts ActiveDataProvider */
/* @var $settlementCostDataProvider ActiveDataProvider */
/* @var $navTypesItems array */

$this->title = Yii::t('provision', 'Generate provisions for: {user}', ['user' => $model->getIssueUser()->getTypeWithUser()]);
$this->params['breadcrumbs'] = array_merge(
	Breadcrumbs::issue($model->getModel(), false),
	Breadcrumbs::settlement($model->getModel())
);
$this->params['breadcrumbs'][] = ['label' => Yii::t('provision', 'Provisions'), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Set provisions');

?>
<div class="provision-settlement-user">

	<p>

		<?= Html::a(
			Yii::t('provision', 'Create provision type'),
			['type/create-settlement', 'id' => $model->getModel()->id, 'issueUserType' => $model->getIssueUser()->type],
			['class' => 'btn btn-success'])
		?>

		<?= Html::a(
			Yii::t('provision', 'Schemas provisions'),
			Url::userProvisions($model->getIssueUser()->user_id, $model->typeId),
			['class' => 'btn btn-info'])
		?>

	</p>

	<div class="row">
		<div class="col-md-6">
			<?= SettlementDetailView::widget([
				'model' => $model->getModel(),
				'withOwner' => false,
			]) ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?= GridView::widget([
					'dataProvider' => $userNotSettledCosts,
					'toolbar' => [
						[
							'content' =>
								Html::a(Html::icon('plus'),
									['/settlement/cost/create', 'id' => $model->getModel()->getIssueId()],
									[
										'class' => 'btn btn-success',
										'title' => Yii::t('provision', 'Create issue cost'),
									]),
						],
					],
					'panel' => [
						'type' => GridView::TYPE_DANGER,
						'heading' => '<i class="fa fa-usd"></i> ' . Yii::t('settlement', 'Costs without settlements'),
						'after' => false,
						'footer' => false,
					],
					'columns' => [
						['class' => IssueColumn::class],
						'typeName',
						[
							'class' => CurrencyColumn::class,
						],
						'date_at:date',
						'settled_at:date',
						[
							'class' => IssueCostActionColumn::class,
							'settlement' => $model->getModel(),
							'settle' => false,
							'link' => true,
						],
					],
				]
			) ?>
		</div>

		<div class="col-md-6">
			<?= GridView::widget([
					'dataProvider' => $settlementCostDataProvider,
					'panel' => [
						'type' => GridView::TYPE_DANGER,
						'heading' => '<i class="fa fa-usd"></i> ' . Yii::t('settlement', 'Settlement costs'),
						'before' => false,
						'after' => false,
						'footer' => false,
					],
					'showPageSummary' => true,
					'columns' => [
						['class' => IssueColumn::class],
						'typeName',
						[
							'class' => CurrencyColumn::class,
							'pageSummary' => true,
						],
						[
							'class' => IssueCostActionColumn::class,
							'settlement' => $model->getModel(),
							'unlink' => true,
						],
					],
				]
			) ?>
		</div>

	</div>

	<?= Nav::widget([
		'items' => $navTypesItems,
		'options' => ['class' => 'nav-pills'],
	]) ?>

	<?= !empty($model->getTypesNames())
		? $this->render('_user_form', ['model' => $model])
		: ''
	?>

	<?= UserProvisionsWidget::widget([
		'userData' => $model->getData(),
		'withFrom' => false,
		'withTypeDetail' => false,
		'extraProvisionsColumns' => [
			[
				'class' => CurrencyColumn::class,
				'contentBold' => false,
				'label' => Yii::t('provision', 'Provision ({currencySymbol})', ['currencySymbol' => Yii::$app->formatter->getCurrencySymbol()]),
				'value' => static function (ProvisionUser $data) use ($model): Decimal {
					return $model->getProvisionsSum($data);
				},
			],
			[
				'class' => CurrencyColumn::class,
				'label' => Yii::t('provision', 'Base Value'),
				'value' => static function (ProvisionUser $data) use ($model): ?Decimal {
					if ($data->type->is_percentage) {
						return $model->getPaysSumWithoutGeneralCosts();
					}
					return null;
				},
			],
		],
	]) ?>


</div>
