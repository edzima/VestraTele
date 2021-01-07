<?php

use backend\modules\provision\models\ProvisionTypeForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ProvisionTypeForm */

$this->title = 'Create provision type';
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Provisions'), 'url' => ['/provision/provision']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Provisions types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-type-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
