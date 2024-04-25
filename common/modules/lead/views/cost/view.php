<?php

use common\modules\lead\models\LeadCost;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var LeadCost $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['url' => ['lead/index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-cost-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'campaign',
			'value',
			'date_at',
			'created_at',
			'updated_at',
		],
	]) ?>

</div>
