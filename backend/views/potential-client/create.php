<?php

use common\models\PotentialClient;

/** @var yii\web\View $this */
/** @var PotentialClient $model */

$this->title = Yii::t('common', 'Create Potential Client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Potential Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="potential-client-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
