<?php

use common\helpers\Html;
use common\models\issue\IssueTag;
use yii\web\View;

/* @var $this View */
/* @var $models IssueTag[] */

$labelClass = function (string $type = null): string {
	switch ($type) {
		case IssueTag::TYPE_CLIENT:
			return 'label-danger';
		case IssueTag::TYPE_SETTLEMENT:
			return 'label-warning';
		default:
			return 'label-primary';
	}
}

?>

<?php if (!empty($models)): ?>
	<span class="badges-wrapper">
	<?php foreach ($models as $model): ?>
		<?= Html::a(Html::encode($model->name), ['index', 'IssueSearch[tagsIds]' => $model->id],
			[
				'class' => 'label ' . $labelClass($model->type),
			])
		?>

	<?php endforeach; ?>
</span>

<?php endif; ?>
