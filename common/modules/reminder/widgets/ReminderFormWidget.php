<?php

namespace common\modules\reminder\widgets;

use common\helpers\ArrayHelper;
use common\modules\reminder\models\ReminderForm;
use yii\base\Widget;

class ReminderFormWidget extends Widget {

	public array $options = [
		'id' => 'reminder-form',
	];

	public ReminderForm $model;

	/**
	 * @var Closure|array
	 */
	public $users;

	public function run(): string {

		$usersOptions = $this->users;
		$usersItems = ArrayHelper::remove($usersOptions, 'items', []);

		return $this->render('form', [
			'model' => $this->model,
			'options' => $this->options,
			'usersOptions' => $usersOptions,
			'usersItems' => $usersItems,
		]);
	}
}
