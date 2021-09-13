<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-08
 * Time: 11:58
 */

namespace backend\modules\issue\models;

use common\models\forms\HiddenFieldsModel;
use common\models\issue\IssueNote;
use common\models\issue\IssueNoteForm as BaseForm;

class IssueNoteForm extends BaseForm implements HiddenFieldsModel {

	public const SCENARIO_TEMPLATE = 'template';

	public bool $is_template = false;

	public function rules(): array {
		return array_merge(parent::rules(), [
			['is_template', 'boolean', 'on' => static::SCENARIO_TEMPLATE],
		]);
	}

	public function setModel(IssueNote $model): void {
		parent::setModel($model);
		$this->is_template = $model->is_template;
	}

	protected function beforeSave(): bool {
		$before = parent::beforeSave();
		$this->getModel()->is_template = $this->is_template;
		return $before;
	}

	public function isVisibleField(string $attribute): bool {
		if ($attribute !== 'is_template') {
			return true;
		}
		return $this->scenario === static::SCENARIO_TEMPLATE;
	}
}
