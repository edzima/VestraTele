<?php

use common\models\PotentialClient;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PotentialClient */

$this->title = Yii::t('common', 'Create Potential Client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Potential Clients'), 'url' => ['self']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="potential-client-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
