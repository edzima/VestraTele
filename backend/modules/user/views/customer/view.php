<?php

use common\models\user\Customer;
use common\widgets\address\AddressDetailView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Customer */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
	</p>


	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'profile.firstname',
			'profile.lastname',
			'email',
			'profile.phone',
			'profile.phone_2',
			'statusName',
		],
	]) ?>

	<?= $model->homeAddress ? AddressDetailView::widget(['model' => $model->homeAddress]) : '' ?>



</div>

