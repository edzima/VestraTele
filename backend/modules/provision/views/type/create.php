<?php

use backend\modules\provision\models\ProvisionTypeForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ProvisionTypeForm */

$this->title = 'Dodaj typ prowizji';
$this->params['breadcrumbs'][] = ['label' => 'Typy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provision-type-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
