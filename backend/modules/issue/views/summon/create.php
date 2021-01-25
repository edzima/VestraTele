<?php

/* @var $this yii\web\View */

/* @var $model SummonForm */

use backend\modules\issue\models\SummonForm;

$this->title = Yii::t('backend', 'Create summon');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Summons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summon-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
