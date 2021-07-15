<?php

namespace common\tests\unit;

use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderForm;
use common\tests\_support\UnitModelTrait;
use yii\test\ActiveFixture;

class ReminderTest extends Unit {

	use UnitModelTrait;

	private ReminderForm $model;

	public function _before() {
		parent::_before();
	}

	public function _fixtures(): array {
		return [
			'reminder' => [
				'class' => ActiveFixture::class,
				'modelClass' => Reminder::class,
				'dataFile' => codecept_data_dir() . 'reminder.php',
			],
		];
	}

	public function testEmpty(): void {
		$this->giveModel(null, null, null);
		$this->thenUnsuccessValidate();
	}

	public function testSaveWithoutDetails(): void {
		$this->giveModel(
			Reminder::PRIORITY_HIGH,
			'2020-01-01 12:00'
			, null);
		$this->thenSuccessSave();
		$this->thenSeeReminder(
			Reminder::PRIORITY_HIGH,
			'2020-01-01 12:00',
		);
	}

	public function testSaveWithDetails(): void {
		$this->giveModel(
			Reminder::PRIORITY_HIGH,
			'2020-01-01 12:00',
			'Some details'
		);
		$this->thenSuccessSave();
		$this->thenSeeReminder(
			Reminder::PRIORITY_HIGH,
			'2020-01-01 12:00',
			'Some details',
		);
	}

	private function giveModel($priority, $datetime, $details): void {
		$this->model = new ReminderForm([
			'priority' => $priority,
			'date_at' => $datetime,
			'details' => $details,
		]);
	}

	private function thenSeeReminder(int $priority, string $date_at, ?string $details = null) {
		$this->tester->seeRecord(Reminder::class, [
			'priority' => $priority,
			'date_at' => $date_at,
			'details' => $details,
		]);
	}

	public function getModel(): ReminderForm {
		return $this->model;
	}

}
