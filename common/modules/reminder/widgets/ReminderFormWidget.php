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

	public $fieldsOptions = [
		'date_at' => [
			'options' => [
				'class' => ['col-md-3 col-lg-2'],
			],
		],
		'priority' => [
			'options' => [
				'class' => ['col-md-2 col-lg-1'],
			],
		],
		'user_id' => [
			'options' => [
				'class' => ['col-md-3 col-lg-2'],
			],
		],
		'details' => [
			'options' => [
				'class' => ['col-md-8 col-lg-5'],
			],
		],
	];

	public function run(): string {

		$usersOptions = $this->users;
		$usersItems = ArrayHelper::remove($usersOptions, 'items', []);

		return $this->render('form', [
			'model' => $this->model,
			'options' => $this->options,
			'usersOptions' => $usersOptions,
			'usersItems' => $usersItems,
			'fieldsOptions' => $this->fieldsOptions,
		]);
	}
}
