<?php

namespace common\tests\unit\postal;

use common\components\postal\PocztaPolska;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssueShipmentPocztaPolska;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use Yii;

class IssueShipmentsPocztaPolskaTest extends Unit {

	use UnitModelTrait;

	private PocztaPolska $pocztaPolska;

	private IssueShipmentPocztaPolska $model;

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

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Shipment Number cannot be blank.', 'shipment_number');
		$this->thenSeeError('Issue cannot be blank.', 'issue_id');
	}

	public function testValidateShipmentNumber(): void {
		$this->giveModel([
			'issue_id' => 1,
			'shipment_number' => '12221233122',
		]);
		$this->thenSuccessValidate();

		$this->giveModel([
			'issue_id' => 1,
			'shipment_number' => '(00)12221233122',
		]);
		$this->thenSuccessValidate();
		$this->tester->assertSame('0012221233122', $this->model->shipment_number);

		$this->giveModel([
			'issue_id' => 1,
			'shipment_number' => '(00) 12221-233122',
		]);
		$this->thenSuccessValidate();
		$this->tester->assertSame('0012221233122', $this->model->shipment_number);
	}

	public function testSaveApiData(): void {
		$this->pocztaPolska->checkShipment('testp0');
		$shipment = $this->pocztaPolska->getShipment();
		$this->tester->assertNotNull($shipment);
		$this->giveModel();
		$model = $this->getModel();
		$model->issue_id = 1;
		$model->setShipment($shipment);
		$this->tester->assertTrue($model->save());
		if ($shipment->danePrzesylki && $shipment->danePrzesylki->zakonczonoObsluge) {
			$this->tester->assertNotNull($model->finished_at);
		}
	}

	public function giveModel(array $config = []) {
		$this->model = new IssueShipmentPocztaPolska($config);
	}

	public function getModel(): IssueShipmentPocztaPolska {
		return $this->model;
	}
}
