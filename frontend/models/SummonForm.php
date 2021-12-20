<?php

namespace frontend\models;

use common\models\forms\HiddenFieldsModel;
use common\models\issue\form\SummonForm as BaseSummonForm;

/**
 * Form model for Summon in frontend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonForm extends BaseSummonForm implements HiddenFieldsModel {

	public function isVisibleField(string $attribute): bool {
		return $this->isAttributeActive($attribute);
	}
}
