<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\SummonDoc */

$this->title = Yii::t('backend', 'Create Summon Doc');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['/issue/summon/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Summon Docs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-doc-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
