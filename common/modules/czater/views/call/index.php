<?php

use common\widgets\GridView;

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ArrayDataProvider */

$this->title = Yii::t('czater', 'Calls');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
]) ?>
