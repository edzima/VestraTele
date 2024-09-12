<?php

use common\modules\lead\models\import\FBAdsCostImport;
use common\widgets\ActiveForm;
use kartik\sortable\Sortable;
use kartik\sortinput\SortableInput;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var FBAdsCostImport $model */

$this->title = Yii::t('lead', 'Import FB Ads Cost');
$this->params['breadcrumbs'][] = ['url' => ['lead/index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-cost-import-pixel">

	<h1><?= Html::encode($this->title) ?></h1>


	<div class="lead-cost-import-pixel-form">
		<?php $form = ActiveForm::begin(); ?>

		<div class="row">
			<?= $form->field($model, 'file', [
				'options' => ['class' => 'col-md-6 col-lg-2'],
			])->fileInput([
				'accept' => '.csv',
			]) ?>

			<?= $form->field($model, 'createCampaigns', [
				'options' => ['class' => 'col-md-6 col-lg-2'],
			])->checkbox() ?>

		</div>

		<div class="row">
			<div class="col-md-10 col-lg-6">
				<?= $form->field($model, 'sortableColumns')
					->widget(SortableInput::class, [
						'items' => $model->getSortableItemsData(),
						'sortableOptions' => [
							'type' => Sortable::TYPE_GRID,
						],
					]) ?>

			</div>
		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Import'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>


</div>
