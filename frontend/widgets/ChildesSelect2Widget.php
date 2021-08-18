<?php

namespace frontend\widgets;

use common\models\user\User;
use kartik\select2\Select2;
use Yii;
use yii\base\InvalidConfigException;

class ChildesSelect2Widget extends Select2 {

	public const TYPE_ALL = 'all';
	public const TYPE_DIRECT = 'direct';

	public string $type = self::TYPE_ALL;
	public $user_id;

	public $showToggleAll = false;

	public ?string $redirectGetParam = null;

	public function init(): void {
		parent::init();
		if ($this->user_id === null) {
			$this->user_id = Yii::$app->user->getId();
		}
		if ($this->user_id === null) {
			throw new InvalidConfigException('$userId cannot be blank.');
		}
		if (!isset($this->options['placeholder'])) {
			$this->options['placeholder'] = $this->defaultPlaceholder();
		}

		$this->data = $this->getData();
		if ($this->redirectGetParam) {
			if (empty($this->value)) {
				$this->value = Yii::$app->request->get($this->redirectGetParam);
			}
			if (isset($this->pluginEvents['select2:select'])) {
				Yii::warning('Overwrite select2.select plugin event by redirect GET param.');
			}
			$this->pluginEvents['select2:select'] = $this->redirectExpression();
		}
	}

	public function run() {
		if (empty($this->data)) {
			return '';
		}
		parent::run();
	}

	public function getData(): array {
		switch ($this->type) {
			case static::TYPE_ALL:
				return User::getSelectList(Yii::$app->userHierarchy->getAllChildesIds($this->user_id));
			case static::TYPE_DIRECT:
				return User::getSelectList(Yii::$app->userHierarchy->getChildesIds($this->user_id));
		}
		throw new InvalidConfigException('$type is not correct.');
	}

	public function redirectExpression(): string {
		$getParam = $this->redirectGetParam ?: 'user_id';
		$js = <<<JS
		function(event){
			let id = event.params.data.id;
			if(id){
				if ('URLSearchParams' in window) {
					var searchParams = new URLSearchParams(window.location.search);
					searchParams.set('$getParam', id);
					window.location.search = searchParams.toString();
				}else{
					alert('Update Browser');
				}
			}
		}
JS;
		return $js;
	}

	protected function defaultPlaceholder(): string {
		return Yii::t('common', 'User');
	}
}
