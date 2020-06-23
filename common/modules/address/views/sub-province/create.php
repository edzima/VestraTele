<?php

use common\models\address\SubProvince;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model SubProvince */

$this->title = 'Dodaj Gmine';
$this->params['breadcrumbs'][] = ['label' => 'Gminy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-province-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
