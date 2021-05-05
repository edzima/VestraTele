<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\hint\HintSource */

$this->title = Yii::t('hint', 'Create Hint Source');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hint-source-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
