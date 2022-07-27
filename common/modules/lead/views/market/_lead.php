<?php

use common\helpers\Html;
use common\modules\lead\models\ActiveLead;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model ActiveLead */

?>


<div class="lead-market-lead">
	<h3>
		<?= Html::a(Html::encode($model->getName()), ['lead/view', 'id' => $model->getId()]) ?>
	</h3>
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'status',
			[
				'attribute' => 'source.type.nameWithDescription',
				'label' => Yii::t('lead', 'Type'),
			],
			'source',
			[
				'attribute' => 'campaign',
				'visible' => !empty($model->campaign_id),
			],
			'date_at:datetime',
			[
				'attribute' => 'phone',
				'format' => 'tel',
				'visible' => !empty($model->getPhone()),
			],
			[
				'attribute' => 'email',
				'format' => 'email',
				'visible' => !empty($model->getEmail()),
			],
			[
				'attribute' => 'postal_code',
				'visible' => !empty($model->postal_code),
			],
			[
				'attribute' => 'providerName',
				'visible' => !empty($model->provider),
			],
		],
	]) ?>

</div>
