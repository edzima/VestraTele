<?php

use common\modules\czater\entities\Client;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Client */

if ($model->name) {
	$this->title = Yii::t('czater', 'Client: {name}', ['name' => $model->name]);
} else {
	$this->title = Yii::t('czater', 'Client: {id}', ['id' => $model->idClient]);
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('czater', 'Clients'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>


<?= DetailView::widget([
	'model' => $model,
]) ?>
