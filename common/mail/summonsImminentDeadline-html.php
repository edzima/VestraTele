<?php

use common\helpers\Html;
use common\models\issue\Summon;
use frontend\helpers\Url;

/* @var $this yii\web\View */
/* @var $models Summon[] */

if (count($models) === 1) {
	$model = reset($models);
	$this->title = Yii::t('issue', 'You have one Summon with imminent deadline.');
	$this->params['primaryButtonText'] = Yii::t('issue', 'View');
	$this->params['primaryButtonHref'] = Yii::getAlias('@frontendUrl') . Url::toRoute(['/summon/view', 'id' => $model->id]);
} else {
	$this->title = Yii::t('issue', 'You have Summons with imminent deadline: <strong>{count}</strong>', [
		'count' => count($models),
	]);
}

$dateModels = [];
foreach ($models as $model) {
	$dateModels[$model->deadline_at][] = $model;
}

?>


<div class="summons-list">
	<?php foreach ($dateModels as $deadline => $summons): ?>
		<table align='center' style='text-align:center'>
			<tr>
				<td align='center' style='text-align:center'>
					<h3><?= Yii::$app->formatter->asDate($deadline) ?></h3>
				</td>
			</tr>
		</table>

		<?php foreach ($summons as $model): ?>
			<?php
			/** @var Summon $model */
			?>
			<div class="summon-imminent-deadline">
				<?= Html::a(Html::encode($model->title ? $model->title : $model->getName()),
					Yii::getAlias('@frontendUrl') . Url::toRoute(['/summon/view', 'id' => $model->id])
				) ?>
				<ul>
					<?php if (!empty($model->title)): ?>
						<li><?= $model->getAttributeLabel('type') . ': ' . Html::encode($model->getTypeName()) ?></li>
					<?php endif; ?>
					<li><?= $model->getAttributeLabel('status') . ': ' . Html::encode($model->getStatusName()) ?></li>
					<?php if ($model->getEntityWithCity()): ?>
						<li><?= $model->getAttributeLabel('entityWithCity') . ': ' . Html::encode($model->getEntityWithCity()) ?></li>
					<?php endif; ?>
				</ul>


			</div>
		<?php endforeach; ?>

	<?php endforeach; ?>
</div>
