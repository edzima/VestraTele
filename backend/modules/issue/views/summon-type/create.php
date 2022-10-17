<?php

use backend\helpers\Html;
use backend\modules\issue\models\SummonTypeForm;

/* @var $this yii\web\View */
/* @var $model SummonTypeForm */

$this->title = Yii::t('backend', 'Create Summon Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['/issue/summon/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Summon Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-type-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
