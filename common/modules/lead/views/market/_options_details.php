<?php

use common\modules\lead\models\entities\LeadMarketOptions;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model LeadMarketOptions */

?>

<div class="lead-market-details">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'visibleAreaName',
			'visibleAddressDetails:boolean',
		],
	])
	?>
</div>
