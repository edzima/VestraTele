<?php

use common\models\issue\IssueUser;
use common\modules\issue\widgets\IssueUsersWidget;

/* @var $this yii\web\View */
/* @var $users IssueUser[] */
/* @var $viewUrl Closure| null */
/* @var $widget IssueUsersWidget */
?>

<div class="row issue-users-row">
	<?php foreach ($users as $user): ?>
		<?= $widget->renderUser($user) ?>
	<?php endforeach; ?>
</div>
