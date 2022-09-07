<?php

use backend\modules\hint\models\HintCityForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model HintCityForm */

$this->title = Yii::t('hint', 'Create Hint City');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hint-city-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
