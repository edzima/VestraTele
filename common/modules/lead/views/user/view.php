<?php

use common\modules\lead\models\LeadUser;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadUser */

$this->title = $model->getUserWithTypeName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->lead->getName(), 'url' => ['lead/view', 'id' => $model->lead_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-user-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'lead_id' => $model->lead_id, 'user_id' => $model->user_id, 'type' => $model->type], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Delete'), ['delete', 'lead_id' => $model->lead_id, 'user_id' => $model->user_id, 'type' => $model->type], [
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
			'lead_id',
			'user_id',
			'type',
			'created_at:datetime',
			'updated_at:datetime',
		],
	]) ?>

</div>
