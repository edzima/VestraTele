<?php

use common\modules\czater\entities\Call;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Call */
if (empty($model->clientName)) {
	$name = $model->clientNumber;
} else {
	$name = $model->clientName . ' - ' . $model->clientNumber;
}
$this->title = Yii::t('czater', 'Call: {name}', ['name' => $name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('czater', 'Calls'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>

<?= DetailView::widget([
	'model' => $model,
]) ?>
