<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Score */
$this->title = 'Create Score';
$this->params['breadcrumbs'][] = ['label' => 'Scores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'connexion' => $connexion,
		'tele' => $tele,
    ]) ?>

</div>