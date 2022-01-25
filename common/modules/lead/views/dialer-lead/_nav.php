<?php

use common\modules\lead\models\searches\LeadDialerSearch;
use yii\bootstrap\Nav;
use yii\web\View;

/* @var $this View */
/* @var $model LeadDialerSearch */

$navDialersItems = [];
if (count($model::getDialersNames()) > 1) {
	foreach ($model::getDialersNames() as $id => $name) {
		$navDialersItems[] = [
			'label' => $name,
			'url' => ['index', 'dialerId' => $id],
			'active' => $id === $model->getDialerId(),
		];
	}
}

?>

<div class="dialer-lead-nav">
	<?= !empty($navDialersItems)
		? Nav::widget([
				'items' => $navDialersItems,
				'options' => ['class' => 'nav-pills'],
			]
		)
		: ''
	?>
</div>




