<?php

use common\modules\czater\entities\Call;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Call */

$this->title = Yii::t('czater', 'Call: {name}', ['name' => $model->clientName . ' - ' . $model->clientNumber]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('czater', 'Calls'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>

<?= DetailView::widget([
	'model' => $model,
]) ?>
