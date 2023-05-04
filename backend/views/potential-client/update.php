<?php

use common\models\PotentialClient;

/** @var yii\web\View $this */
/** @var PotentialClient $model */

$this->title = Yii::t('common', 'Update Potential Client: {name}', [
	'name' => $model->getName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Potential Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="potential-client-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
