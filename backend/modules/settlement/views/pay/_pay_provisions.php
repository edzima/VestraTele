<?php

use common\widgets\grid\ProvisionUserGrid;
use yii\data\DataProviderInterface;

/** @var $dataProvider DataProviderInterface */

?>

<?= ProvisionUserGrid::widget([
	'dataProvider' => $dataProvider,
	'caption' => Yii::t('backend', 'Provisions'),
]) ?>

