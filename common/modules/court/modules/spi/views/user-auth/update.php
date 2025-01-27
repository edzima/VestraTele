<?php

use common\modules\court\modules\spi\models\SpiUserAuthForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var SpiUserAuthForm $model */

$this->title = Yii::t('spi', 'Update Spi Auth: {name}', [
	'name' => $model->getModel()->user->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('spi', 'SPI User Auths'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->id, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('spi', 'Update');
?>
<div class="spi-user-auth-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
