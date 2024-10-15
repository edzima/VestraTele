<?php

use common\models\settlement\SettlementType;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var SettlementType $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlements'), 'url' => ['calculation/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Settlement Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settlement-type-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-4">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'name',
					'is_active:boolean',
					[
						'attribute' => 'issueTypes',
						'value' => function (SettlementType $model) {
							$types = $model->issueTypes;
							if (empty($types)) {
								return Yii::t('settlement', 'All types');
							}
							$names = [];
							foreach ($types as $type) {
								$names[] = $type->name;
							}
							return Html::ul($names);
						},
						'format' => 'html',
					],
				],
			]) ?>
		</div>
		<div class="col-md-3">
			<?= $this->render('_options-view', [
				'model' => $model->getTypeOptions(),
			]) ?>
		</div>
	</div>


</div>
