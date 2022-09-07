<?php

use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $articlesDataProvider null|ActiveDataProvider */

$this->title = Yii::$app->name;
?>
<div class="site-index">

	<?php if ($articlesDataProvider !== null && $articlesDataProvider->getTotalCount() > 0): ?>
		<?= ListView::widget([
			'summary' => '',
			'dataProvider' => $articlesDataProvider,
			'itemView' => '_article',
		]) ?>
	<?php endif; ?>
</div>
