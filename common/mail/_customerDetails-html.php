<?php

use common\helpers\Html;
use common\models\user\Customer;

/**
 * @var Customer $user
 */
?>

<div class="customer-detail-email">
	<p><?= Yii::t('issue', 'Customer: {name}', ['name' => $user->getFullName()]) ?> </p>

	<?php if ($user->profile->hasPhones()): ?>
		<p><?= Yii::t('common', 'Phone') ?>: <?= Html::telLink($user->getPhone()) ?></p>
	<?php endif; ?>

	<?php if (!empty($user->email)): ?>
		<p><?= Yii::t('common', 'Email') ?>: <?= Html::mailto($user->email) ?></p>
	<?php endif; ?>
</div>
