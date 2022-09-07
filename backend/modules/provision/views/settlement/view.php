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

		<?= Html::a(Yii::t('backend', 'Without provisions'),
			['/settlement/calculation/without-provisions'],
			['class' => 'btn btn-warning'])
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

	<div class="row">
		<div class="col-md-4 col-lg-3">
			<?= SettlementDetailView::widget([
				'model' => $model,
			]) ?>
		</div>
		<div class="col-md-8 col-lg-9">
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'summary' => false,
				'caption' => Yii::t('provision', 'Provisions'),
				'columns' => [
					[
						'attribute' => 'pay.partInfo',
						'visible' => $model->getPaysCount() > 1,
					],
					'type.name',
					'toUser',
					'fromUserString',
					'value:currency',
					'provision',
					'pay.value:currency',
					[
						'class' => ActionColumn::class,
						'controller' => '/provision/provision',
						'template' => '{update} {delete}',
					],
				],
			]) ?>

		</div>
	</div>


	<h3><?= Yii::t('common', 'Users') ?></h3>

	<div class="row users-grid-wrapper">
		<?php foreach ($userModels as $userModel) {

			echo $this->render('_user_types', [
				'model' => $userModel,
			]);
		}
		?>
	</div>


</div>
