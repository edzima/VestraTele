<?php

use common\helpers\Html;
use common\models\issue\IssueTag;
use yii\web\View;

/* @var $this View */
/* @var $models IssueTag[] */

?>

<?php if (!empty($models)): ?>
	<span class="badges-wrapper">
	<?php foreach ($models as $model): ?>
		<?= Html::a(Html::encode($model->name), ['index', 'IssueSearch[tagsIds]' => $model->id],
			[
				'class' => 'badge badge-secondary',
			])
		?>

	<?php endforeach; ?>
</span>

<?php endif; ?>
