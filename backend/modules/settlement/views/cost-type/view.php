<?php

use common\models\settlement\CostType;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var CostType $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['url' => ['cost/index'], 'label' => Yii::t('settlement', 'Costs')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('settlement', 'Cost Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="cost-type-view">

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

		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'name',
					'is_active:boolean',
					'is_for_settlement:boolean',
				],
			]) ?>

			<?= $this->render('_options-view', [
				'model' => $model->getTypeOptions(),
			]) ?>
		</div>
	</div>


</div>
