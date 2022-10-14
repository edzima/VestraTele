<?php

use common\models\issue\Summon;
use common\models\SummonTypeOptions;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\issue\SummonType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['/issue/summon/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Summon Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="summon-type-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'id',
					'name',
					'short_name',
					'calendar_background',
				],
			]) ?>
		</div>
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model->getOptions(),
				'attributes' => [
					'showOnTop:boolean',
					'title',
					[
						'attribute' => 'status',
						'value' => Summon::getStatusesNames()[$model->getOptions()->status] ?? null,
					],
					[
						'attribute' => 'formFields',
						'value' => static function (SummonTypeOptions $options): string {
							$fields = $options->getFormAttributesNames();
							if (empty($fields)) {
								return Yii::t('common', 'All');
							}
							return Html::ul($fields);
						},
						'label' => $model->getOptions()->getAttributeLabel('formAttributes'),
						'format' => 'html',
					],
					[
						'attribute' => 'visibleFields',
						'value' => static function (SummonTypeOptions $options): string {
							$fields = $options->getVisibleSummonAttributesNames();
							if (empty($fields)) {
								return Yii::t('common', 'All');
							}
							return Html::ul($fields);
						},
						'label' => $model->getOptions()->getAttributeLabel('visibleSummonFields'),
						'format' => 'html',
					],
					[
						'attribute' => 'requiredFields',
						'value' => static function (SummonTypeOptions $options): string {
							$fields = $options->getRequiredFieldsNames();
							if (empty($fields)) {
								return Yii::t('common', 'Default');
							}
							return Html::ul($fields);
						},
						'label' => $model->getOptions()->getAttributeLabel('requiredFields'),
						'format' => 'html',
					],
				],
			]) ?>
		</div>
	</div>


</div>
