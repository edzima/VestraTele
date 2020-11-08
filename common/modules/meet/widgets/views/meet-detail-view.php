<?php

use common\models\issue\IssueMeet;
use common\widgets\address\AddressDetailView;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model IssueMeet */
?>


<h3>Lead</h3>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'type',
		'statusName',
		'details:ntext',
		'agent',
		'campaignName',
		'date_at:datetime',
		'date_end_at:datetime',
		'created_at:datetime',
		'updated_at:datetime',
	],
]) ?>

<h3>Klient</h3>

<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		'client_name',
		'client_surname',
		'phone',
		'email:email',
	],
]) ?>

<h3>Adres</h3>

<?= $model->customerAddress ? AddressDetailView::widget([
	'model' => $model->customerAddress,
]) : '' ?>

<?= AddressDetailView::widget([
	'model' => $model->getAddress(),
]) ?>

<style>
	.table > tbody > tr > th {
		width: 30%;
	}
</style>
