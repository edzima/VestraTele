<?php

use yii\helpers\HtmlPurifier;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCategory */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $menuItems array */

$this->title = Yii::t('frontend', 'Articles');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="article-category">
	<h1><?= $model->title ?></h1>

	<div class="row">
		<div class="col-md-9">
			<div class="comment">
				<?= HtmlPurifier::process($model->comment) ?>
			</div>

			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_item',
				'summary' => false,
			]) ?>
		</div>

		<div class="col-md-3">
			<?= $this->render(
				'_categoryItem.php',
				['menuItems' => $menuItems]
			) ?>
		</div>
	</div>
</div>
