<?php

namespace frontend\controllers;

use common\modules\issue\controllers\SummonDocLinkController;

class SummonDocController extends SummonDocLinkController {

	public bool $sendEmailAboutToConfirm = true;
}
