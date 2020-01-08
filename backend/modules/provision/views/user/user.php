<?php

use backend\modules\provision\models\ProvisionUserForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ProvisionUserForm */

$this->title = 'Prowizje: ' . Html::encode($model->getUser()->getFullName());
$this->params['breadcrumbs'][] = ['label' => 'Provision Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-user-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Nowy typ', ['type/create'], ['class' => 'btn btn-primary', 'target' => '_blank']) ?>
	</p>

	<?= $this->render('_multi-form', [
		'model' => $model,
	]) ?>

</div>
