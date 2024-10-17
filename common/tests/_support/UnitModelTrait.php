<?php

namespace common\tests\_support;

use yii\base\InvalidCallException;
use yii\base\Model;

trait UnitModelTrait {

	abstract public function getModel(): Model;

	public function thenSuccessValidate($attributeNames = null, $clearErrors = true): void {
		$validate = $this->getModel()->validate($attributeNames, $clearErrors);
		if (!$validate) {
			codecept_debug($this->getModel()->getErrors());
		}
		$this->tester->assertTrue($validate);
	}

	public function thenUnsuccessValidate($attributeNames = null, $clearErrors = true): void {
		$validate = $this->getModel()->validate($attributeNames, $clearErrors);
		$this->tester->assertFalse($validate);
		codecept_debug($this->getModel()->getErrors());
	}

	public function thenSuccessSave(): void {
		if (!$this->getModel()->hasMethod('save')) {
			throw new InvalidCallException('$model: ' . $this->getModel()::className() . ' has not save() method.');
		}
		$save = (bool) $this->getModel()->save();
		if (!$save) {
			codecept_debug($this->getModel()->getErrors());
		}
		$this->tester->assertTrue($save);
	}

	public function thenUnsuccessSave(): void {
		if (!$this->getModel()->hasMethod('save')) {
			throw new InvalidCallException('$model: ' . $this->getModel()::className() . ' has not save() method.');
		}
		$save = (bool) $this->getModel()->save();
		$this->tester->assertFalse($save);
	}

	public function thenSeeError(string $message, string $attribute): void {
		codecept_debug($this->model->getErrors($attribute));
		$this->tester->assertContains($message, $this->model->getErrors($attribute));
	}

	public function thenDontSeeError(string $attribute): void {
		$this->tester->assertEmpty($this->getModel()->getErrors($attribute));
	}
}
