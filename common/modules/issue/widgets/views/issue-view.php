<?php

use common\models\issue\Issue;
use common\widgets\address\AddressDetailView;
use yii\helpers\Html;
use yii\widgets\DetailView;

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
			<fieldset class="col-md-6">
				<?= DetailView::widget([
					'model' => $model,
					'options' => [
						'class' => 'table table-striped table-bordered detail-view th-nowrap',
					],
					'attributes' => ['agent'],
				]);
				?>
			</fieldset>
			<?php if ($model->hasLawyer()): ?>
				<fieldset class="col-md-6">
					<?= DetailView::widget([
						'model' => $model,
						'options' => [
							'class' => 'table table-striped table-bordered detail-view th-nowrap',
						],
						'attributes' => [
							[
								'attribute' => 'lawyer',
								'visible' => $model->hasLawyer(),
								'label' => 'Prawnik',
							],
						],
					]);
					?>
				</fieldset>
			<?php endif; ?>
		</div>

		<fieldset>
			<legend>Identyfikacja Sprawy
				<button class="btn toggle pull-right" data-toggle="#base-details">
					<i class="glyphicon glyphicon-chevron-down"></i></button>
			</legend>
			<?= DetailView::widget([
				'id' => 'base-details',
				'model' => $model,
				'options' => [
					'class' => 'table table-striped table-bordered detail-view th-nowrap',
				],
				'attributes' => [
					'longId',
					'payed:boolean',
					[
						'attribute' => 'archives_nr',
						'visible' => $model->isArchived(),
					],
					[
						'attribute' => 'tele',
						'visible' => $model->hasTele(),
						'label' => 'Telemarketer',
					],
					'created_at:date',
					'updated_at:date',
					'date:date',
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
					'provision_base',
					[
						'attribute' => 'provision',
						'label' => 'Rodzaj/krotność',
					],

				],
			]) ?>
		</fieldset>

		<div class="row">
			<fieldset class="col-md-6">
				<legend>Klient
					<button class="btn toggle pull-right" data-toggle="#client-details">
						<i class="glyphicon glyphicon-chevron-down"></i></button>
				</legend>

				<?= DetailView::widget([
					'id' => 'client-details',
					'model' => $model,
					'attributes' => [
						'client_first_name',
						'client_surname',

						'client_phone_1',
						'client_phone_2',
						'client_email:email',
					],
				]) ?>
				<?= AddressDetailView::widget([
					'model' => $model->getClientAddress(),
				]) ?>

			</fieldset>

			<fieldset class="col-md-6">
				<legend>
					<button class="btn toggle pull-right" data-toggle="#victim-details">
						<i class="glyphicon glyphicon-chevron-down"></i></button>
				</legend>

				<?= DetailView::widget([
					'id' => 'victim-details',
					'model' => $model,
					'attributes' => [
						'victim_first_name',
						'victim_surname',
						'victim_phone',
						'victim_email:email',
					],
				]) ?>

				<?= AddressDetailView::widget([
					'model' => $model->getVictimAddress(),
				]) ?>
			</fieldset>
		</div>
	</div>

</fieldset>

