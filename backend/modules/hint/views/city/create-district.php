<?php

use backend\helpers\Html;
use backend\modules\hint\models\HintDistrictForm;

/* @var $this \yii\web\View */
/* @var $model HintDistrictForm */

$this->title = Yii::t('hint', 'Create Hint District');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hint-city-create-district">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_district_form', [
		'model' => $model,
	]) ?>

</div>
