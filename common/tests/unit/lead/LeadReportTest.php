<?php

namespace common\tests\unit\lead;

use common\modules\lead\models\LeadReport;
use common\tests\unit\Unit;
use Yii;

class LeadReportTest extends Unit {

	private LeadReport $model;

	public function testFormattedDateTitleNotUpdated(): void {
		$this->giveModel([
			'created_at' => '2020-01-01 16:00:00',
			'updated_at' => '2020-01-01 16:00:00',
		]);
		$this->tester->assertSame('Report from: '
			. Yii::$app->formatter->asDate($this->model->created_at),
			$this->model->getDateTitle()
		);
	}

	public function testFormattedDateTitleUpdated(): void {
		$this->giveModel([
			'created_at' => '2020-01-01 16:00:00',
			'updated_at' => '2020-01-02 16:00:00',
		]);
		$this->tester->assertSame('Report from: '
			. Yii::$app->formatter->asDate($this->model->created_at)
			. ' ( updated: ' . Yii::$app->formatter->asDate($this->model->updated_at) . ' )',
			$this->model->getDateTitle()
		);
	}

	private function giveModel(array $attributes): void {
		$this->model = new LeadReport($attributes);
	}
}
