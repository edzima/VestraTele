<?php

use common\components\message\MessageTemplate;

/* @var $this yii\web\View */
/* @var $template MessageTemplate|null */

if ($template->primaryButtonText !== null && $template->primaryButtonHref !== null) {
	$this->params['primaryButtonText'] = $template->primaryButtonText;
	$this->params['primaryButtonHref'] = $template->primaryButtonHref;
}

?>
<div class="message-view">
	<?= $template ? $template->getBody() : '' ?>
</div>
