<?php

use backend\helpers\Breadcrumbs;
use backend\helpers\Html;
use common\models\issue\IssuePayCalculation;
use common\models\provision\ProvisionType;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use common\widgets\settlement\SettlementDetailView;
use yii\data\ActiveDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $model IssuePayCalculation */
/* @var $types ProvisionType[] */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('provision', 'Settlement provisions: {type}', ['type' => $model->getTypeName()]);
$this->params['breadcrumbs'] = array_merge(
	Breadcrumbs::issue($model, false),
	Breadcrumbs::settlement($model)
);
$this->params['breadcrumbs'][] = Yii::t('backend', 'Provisions');

?>
<div class="provision-settlement-user">


	<div class="row">
		<div class="col-md-6">
			<?= SettlementDetailView::widget([
				'model' => $model,
				'withValueWithoutCosts' => true,
			]) ?>
		</div>

		<div class="col-md-6">

			<p>
				<?= Html::a(
					Yii::t('provision', 'Provisions types'),
					['type/settlement', 'id' => $model->id],
					['class' => 'btn btn-info'])
				?>
			</p>
			<?

			foreach ($model->issue->users as $issueUser) {
				$userTypes = array_filter($types, function (ProvisionType $type) use ($issueUser): bool {
					return $type->isForIssueUser($issueUser->type);
				});
				echo Html::tag('h4',
					Html::encode($issueUser->getTypeWithUser())
					. Html::a(
						Html::icon('plus'),
						['type/create-settlement', 'id' => $model->id, 'issueUserType' => $issueUser->type], [
						'class' => 'btn btn-success',
						'title' => Yii::t('provision', 'Create provision type'),

					])
				);

				if (!empty($userTypes)) {
					echo Html::ul($userTypes, [
						'item' => function (ProvisionType $type) use ($model): string {
							return 'Generuj: ' . Html::a(
									Html::encode($type->name),
									['user', 'id' => $model->id, 'issueUserType' => $type->getIssueUserType(), 'typeId' => $type->id]
								);
						},
					]);
				}
			}

			?>
		</div>
	</div>

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


</div>
