<?php

use common\modules\court\modules\spi\models\auth\SpiUserAuthForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var SpiUserAuthForm $model */

$this->title = Yii::t('spi', 'SPI Auth for User: {user}', [
	'user' => $model->getModel()->user->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('spi', 'Spi User Auths'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="spi-user-auth-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
