<?php

use common\modules\lead\models\LeadSource;
use common\modules\lead\Module;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadSource */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-source-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Module::getInstance()->allowDelete
			? Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			]) : '' ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'id',
			'name',
			'is_active:boolean',
			'url:url',
			'owner',
			'phone:tel',
			'dialer_phone',
			'call_page_widget_id',
			'sort_index',
			'sms_push_template:ntext',
		],
	]) ?>

</div>
