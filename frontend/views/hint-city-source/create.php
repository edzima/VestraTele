<?php

use frontend\helpers\Html;
use frontend\models\HintCitySourceForm;

/* @var $this \yii\web\View */
/* @var $model HintCitySourceForm */

$this->title = Yii::t('hint', 'Add source to: {name}', ['name' => $model->getHintCity()->getCityNameWithType()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['/hint-city/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_form', ['model' => $model]) ?>
