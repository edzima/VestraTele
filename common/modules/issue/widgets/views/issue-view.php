<?php

use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\widgets\FieldsetDetailView;
use common\widgets\WorkerDetailViewWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */

?>

<fieldset>
	<legend>
		<h1 class="inline"><?= Html::encode($this->title) ?></h1>
		<button class="btn toggle pull-right" data-toggle="#issue-details">
			<i class="glyphicon glyphicon-chevron-down"></i>
		</button>
	</legend>
	<div id="issue-details">


		<div class="row">

			<?= FieldsetDetailView::widget([
				'legend' => IssueUser::getTypesNames()[IssueUser::TYPE_CUSTOMER],
				'toggle' => false,
				'htmlOptions' => [
					'class' => 'col-md-4',
				],
				'detailConfig' => [
					'model' => $model->customer,
					'attributes' => [
						'fullName',
						'email:email',
						'profile.phone',
						'profile.phone_2',
					],
				],
			]) ?>

		</div>

		<?= FieldsetDetailView::widget([
			'legend' => Yii::t('common', 'Issue details'),
			'detailConfig' => [
				'id' => 'base-details',
				'model' => $model,
				'options' => [
					'class' => 'table table-striped table-bordered detail-view th-nowrap',
				],
				'attributes' => [
					'longId',
					[
						'attribute' => 'archives_nr',
						'visible' => $model->isArchived(),
					],
					'created_at:date',
					'updated_at:date',
					'date:date',
					'client_email:email',
					'client_phone_1',
					'client_phone_2',
					'victim_email:email',
					'victim_phone',
					[
						'attribute' => 'accident_at',
						'format' => 'date',
						'visible' => $model->isAccident(),
					],
					[
						'attribute' => 'type',
						'label' => $model->getAttributeLabel('type_id'),
					],
					[
						'attribute' => 'stage',
						'label' => $model->getAttributeLabel('stage_id'),
					],
					[
						'attribute' => 'entityResponsible',
						'label' => $model->getAttributeLabel('entity_responsible_id'),
					],
					'details:text',
				],

			],
		]) ?>

		<div class="row">

			<?= FieldsetDetailView::widget([
				'legend' => IssueUser::getTypesNames()[IssueUser::TYPE_AGENT],
				'toggle' => false,
				'htmlOptions' => [
					'class' => 'col-md-4',
				],
				'detailConfig' => [
					'class' => WorkerDetailViewWidget::class,
					'model' => $model->agent,
				],
			]) ?>

			<?= FieldsetDetailView::widget([
				'legend' => IssueUser::getTypesNames()[IssueUser::TYPE_LAWYER],
				'toggle' => false,
				'htmlOptions' => [
					'class' => 'col-md-4',
				],
				'detailConfig' => [
					'class' => WorkerDetailViewWidget::class,
					'model' => $model->lawyer,

				],
			]) ?>

			<?= FieldsetDetailView::widget([
				'legend' => IssueUser::getTypesNames()[IssueUser::TYPE_TELEMARKETER],
				'toggle' => false,
				'htmlOptions' => [
					'class' => 'col-md-4',
				],
				'detailConfig' => [
					'class' => WorkerDetailViewWidget::class,
					'model' => $model->tele,
				],
			]) ?>

		</div>


	</div>

</fieldset>

