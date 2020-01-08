<?php

use backend\helpers\Url;
use backend\modules\provision\models\ProvisionForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ProvisionForm */

$this->title = 'Edycja Prowizji: ' . $model->toUser;
$this->params['breadcrumbs'][] = ['label' => 'Provisions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->toUser, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="provision-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<h2><?= Html::a($model->issue, Url::issueView($model->issue->id), ['target' => '_blank']) ?></h2>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
