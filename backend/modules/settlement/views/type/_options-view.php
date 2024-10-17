<?php

use common\models\settlement\SettlementTypeOptions;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var SettlementTypeOptions $model */

?>

<div class="settlement-type-options-view">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'attribute' => 'default_value',
				'visible' => !empty($model->default_value),
				'format' => 'currency',
			],
			[
				'attribute' => 'vat',
				'visible' => !empty($model->vat),
			],
			[
				'attribute' => 'deadlineRangeName',
				'visible' => !empty($model->deadline_range),
			],
			[
				'attribute' => 'providerTypeName',
				'visible' => !empty($model->provider_type),
			],
		],
	]) ?>
</div>
