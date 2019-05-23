<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
	<div class="article-view">
		<article class="article-item">
			<h1><?= Html::encode($this->title) ?></h1>

			<div class="article-meta">
				<span class="glyphicon glyphicon-time"></span> <?= Yii::$app->formatter->asDatetime($model->published_at) ?>
				<span class="glyphicon glyphicon-folder-close"></span> <?= Html::a($model->category->title, ['article/category', 'slug' => $model->category->slug]) ?>
			</div>

			<div class="row">
				<div>
					<div class="article-text">
						<?= HtmlPurifier::process($model->body) ?>
					</div>
					<div class="article-span">
						<button type="button" class="btn btn-primary btn-lg">
							<?= $model->start_at ?>
							<span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>
						</button>
						<button id="threshold" type="button" class="btn btn-danger btn-lg" value="<?= $model->point ?>">
							<span class="glyphicon glyphicon-scale" aria-hidden="true"></span>Próg punktowy : <?= $model->point ?>
						</button>
						<button type="button" class="btn btn-primary btn-lg">
							<span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span><?= $model->finish_at ?>
						</button>
					</div>


					<hr/>

					<div>
						<?= GridView::widget([
							'dataProvider' => $dataProvider,
							'id' => 'rank',
							'columns' => [
								['class' => 'kartik\grid\SerialColumn'],
								[
									'class' =>
										'\kartik\grid\DataColumn',
									'attribute' => 'tele',
									'value' => 'tele.username',
									'label' => 'Konsultant',
								],
								[
									'class' =>
										'\kartik\grid\DataColumn',
									'attribute' => 'suma',
									'label' => 'Punkty',
								],
							],
							'responsive' => true,
							'pjax' => true,
							'hover' => true,
							'panel' => [
								'type' => GridView::TYPE_PRIMARY,
								'heading' => '<i class="glyphicon glyphicon-tower"></i> Ranking Konkursowy',
								'footer' => false,
							],
							'toolbar' => false,
						])
						?>

					</div>
				</div>


			</div>

		</article>
	</div>
<?php
$this->registerJs(
	'$("document").ready(function(){
			
			var threshold = parseInt($("#threshold").attr("value"));

			$("tr").each(function(){
				var score = $(this).find("td").eq(2).html();
				if(score>=threshold) {
					$(this).addClass("qualified");
				}
			});		
			
		});'
);
?>