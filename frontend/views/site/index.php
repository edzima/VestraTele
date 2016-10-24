<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="jumbotron">
        <h1><?= Yii::$app->name?></h1>
        <p>
		<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Nowe Spotkanie', ['/task/create'], ['class' => 'btn btn-lg btn-success']).
			Html::a('<i class="glyphicon glyphicon-plus"></i> Raporty', ['/task-status'], ['class' => 'btn btn-lg btn-success']).
			Html::a('<i class="glyphicon glyphicon-tower"></i> Konkursy', ['/article'], ['class' => 'btn btn-lg btn-primary']).
			Html::a('<i class="glyphicon glyphicon-stats"></i> Ranking', ['/score'], ['class' => 'btn btn-danger'])
		
		?>
        </p>
    </div>
</div>
