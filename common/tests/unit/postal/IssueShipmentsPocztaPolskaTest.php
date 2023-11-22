<?php

namespace common\tests\unit\postal;

use common\components\postal\PocztaPolska;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssueShipmentPocztaPolska;
use common\tests\unit\Unit;
use Yii;

class IssueShipmentsPocztaPolskaTest extends Unit {

	private PocztaPolska $pocztaPolska;

	public function _before() {
		$this->pocztaPolska = Yii::$app->pocztaPolska;
		parent::_before();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::shipmetsPocztaPolska()
		);
	}

	public function testSaveApiData(): void {
		$this->pocztaPolska->checkShipment('testp0');
		$shipment = $this->pocztaPolska->getShipment();
		$this->tester->assertNotNull($shipment);
		$model = new IssueShipmentPocztaPolska();
		$model->issue_id = 1;
		$model->setShipment($shipment);
		$this->tester->assertTrue($model->save());
		if ($shipment->danePrzesylki && $shipment->danePrzesylki->zakonczonoObsluge) {
			$this->tester->assertNotNull($model->finished_at);
		}
	}
}
