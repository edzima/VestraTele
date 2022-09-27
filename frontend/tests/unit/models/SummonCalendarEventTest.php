<?php

namespace frontend\tests\unit\models;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Summon;
use common\tests\_support\UnitModelTrait;
use DateTime;
use frontend\models\SummonCalendarEvent;
use frontend\tests\unit\Unit;
use yii\base\Model;

class SummonCalendarEventTest extends Unit {

	use UnitModelTrait;

	private SummonCalendarEvent $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::summon(),
			IssueFixtureHelper::customer(true)
		);
	}

	public function testUpdate(): void {
		$this->giveModel([
			'id' => 1,
		]);

		$this->tester->assertTrue($this->model->updateDate((new DateTime('2022-09-23T08:34:00.000Z'))->format(DATE_ATOM)));

		$this->tester->seeRecord(Summon::class, [
			'id' => 1,
			'realize_at' => '2022-09-23 08:34:00',
		]);
	}

	private function giveModel(array $config = []): void {
		$this->model = new SummonCalendarEvent($config);
	}

	public function testToArray(): void {
		/**
		 * @var Summon $summon
		 */
		$summon = $this->tester->grabFixture(IssueFixtureHelper::SUMMON, 'new');
		$this->giveModel([
			'model' => $summon,
		]);

		$array = $this->model->toArray();
		$this->tester->assertSame((int) $array['id'], $summon->id);
		$this->tester->assertSame($array['start'], $summon->realize_at);
	}

	public function getModel(): Model {
		return $this->model;
	}
}
