<?php

namespace common\models\forms;

interface HiddenFieldsModel {

	public function isVisibleField(string $attribute): bool;
}
