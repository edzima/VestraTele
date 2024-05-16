<?php

namespace common\widgets;

use common\helpers\Html;
use common\models\forms\HiddenFieldsModel;
use yii\bootstrap\ActiveForm as BaseActiveForm;
use yii\widgets\ActiveField;

class ActiveForm extends BaseActiveForm {

	public function field($model, $attribute, $options = []): ActiveField {
		$field = parent::field($model, $attribute, $options);
		if ($model instanceof HiddenFieldsModel && !$model->isVisibleField($attribute)) {
			Html::addCssClass($field->options, 'hidden');
			$field->inputOptions['hidden'] = true;
		}
		return $field;
	}
}
