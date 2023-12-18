<?php

use common\models\issue\Summon;
use frontend\helpers\Url;

/* @var $this yii\web\View */
/* @var $models Summon[] */

?>


<?php foreach ($models as $model): ?>

	<?= $model->title ? $model->title : $model->getName() ?>
	<?= Yii::getAlias('@frontendUrl') . Url::toRoute(['/summon/view', 'id' => $model->id]) ?>

	<?php if (!empty($model->title)): ?>
		<?= $model->getAttributeLabel('type') . ': ' . $model->getTypeName() ?>
	<?php endif; ?>
	<?= $model->getAttributeLabel('status') . ': ' . $model->getStatusName() ?>
	<?php if ($model->getEntityWithCity()): ?>
		<?= $model->getAttributeLabel('entityWithCity') . ': ' . $model->getEntityWithCity() ?>
	<?php endif; ?>


<?php endforeach; ?>

