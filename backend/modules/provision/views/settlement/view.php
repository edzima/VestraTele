<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use backend\modules\provision\models\SettlementUserProvisionsForm;
use common\models\issue\IssuePayCalculation;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use common\widgets\settlement\SettlementDetailView;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $model IssuePayCalculation */
/* @var $dataProvider ActiveDataProvider */
/* @var $userModels SettlementUserProvisionsForm[] */

$this->title = Yii::t('provision', 'Settlement provisions: {type}', ['type' => $model->getTypeName()]);
$this->params['breadcrumbs'] = array_merge(
	Breadcrumbs::issue($model, false),
	Breadcrumbs::settlement($model)
);
$this->params['breadcrumbs'][] = Yii::t('backend', 'Provisions');

?>
<div class="provision-settlement-view">
	<p>
		<?= Html::a(
			Yii::t('backend', 'Generate'),
			['generate', 'id' => $model->id],
			[
				'class' => 'btn btn-success',
				'data-method' => 'POST',
			])
		?>

		<?= Html::a(
			Yii::t('provision', 'Provisions types'),
			['type/settlement', 'id' => $model->id],
			['class' => 'btn btn-info'])
		?>

		<?= $dataProvider->getTotalCount()
			? Html::a(
				Yii::t('provision', 'Delete provisions'),
				['delete', 'id' => $model->id],
				[
					'class' => 'btn btn-danger pull-right',
					'data-method' => 'POST',
					'data-confirm' => Yii::t('backend', 'Are you sure you want to delete all provisions for this settlement?'),
				]
			)
			: ''
		?>
	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'attribute' => 'pay.partInfo',
				'visible' => $model->getPaysCount() > 1,
			],
			'type.name',
			'toUser',
			'fromUserString',
			'value:currency',
			'provisionPercent',
			'pay.value:currency',
			[
				'class' => ActionColumn::class,
				'controller' => '/provision/provision',
				'template' => '{update} {delete}',
			],
		],
	]) ?>


	<div class="row">
		<div class="col-md-4 col-lg-3">
			<?= SettlementDetailView::widget([
				'model' => $model,
			]) ?>
		</div>

		<div class="col-md-8 col-lg-9 users-grid-wrapper">
			<?
			foreach ($userModels as $userModel) {

				echo $this->render('_user_types', [
					'model' => $userModel,
				]);
			}
			?>
		</div>
	</div>


</div>
