<?php

use common\widgets\GridView;
use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ArrayDataProvider */

$this->title = Yii::t('czater', 'Convs');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
]) ?>
