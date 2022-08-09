<?php

use common\modules\lead\models\LeadIssue;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadIssue */

$this->title = $model->lead->getName() . ' - ' . $model->issue_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

?>
<div class="lead-issue-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'lead_id' => $model->lead_id, 'issue_id' => $model->issue_id, 'crm_id' => $model->crm_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Delete'), ['delete', 'lead_id' => $model->lead_id, 'issue_id' => $model->issue_id, 'crm_id' => $model->crm_id], [
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
			[
				'attribute' => 'lead_id',
				'value' => Html::a(Html::encode($model->lead->getName()), [
					'/lead/lead/view', 'id' => $model->lead_id,
				]),
				'format' => 'html',
			],
			[
				'attribute' => 'issue_id',
				'value' => Html::a($model->issue_id,
					$model->crm->backend_url . Yii::$app->urlManager->createUrl(['issue/issue/view', 'id' => $model->issue_id])
				),
				'format' => 'html',
			],
			[
				'attribute' => 'crm_id',
				'value' => Html::a(Html::encode($model->crm->name), [
					'crm/view', 'id' => $model->crm_id,
				]),
				'format' => 'html',
			],
			'created_at:datetime',
		],
	]) ?>

</div>
