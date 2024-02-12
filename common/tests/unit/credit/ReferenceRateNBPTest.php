<?php

namespace common\tests\unit\credit;

use common\modules\credit\components\exception\ReferenceRateNBPXmlGetContentException;
use common\modules\credit\components\ReferenceRateNBPComponent;
use common\modules\credit\models\ReferenceRateNBP;
use common\tests\unit\Unit;
use SimpleXMLElement;
use yii\caching\DummyCache;

class ReferenceRateNBPTest extends Unit {

	private ReferenceRateNBPComponent $referenceRateNBP;

	public function _before() {
		$this->referenceRateNBP = new ReferenceRateNBPComponent([
			'cache' => [
				'class' => DummyCache::class,
			],
		]);
	}

	public function testGetXmlFromArchive() {
		$xml = $this->referenceRateNBP->getXMLFromArchive();
		$this->tester->assertInstanceOf(SimpleXMLElement::class, $xml);
	}

	public function testGetXmlFromArchiveWithInvalidPath() {
		$this->referenceRateNBP->archivePath = 'http://invalid-path.com';
		$this->tester->expectThrowable(ReferenceRateNBPXmlGetContentException::class, function () {
			$this->referenceRateNBP->getXMLFromArchive();
		});
	}

	public function testModels() {
		$models = $this->referenceRateNBP->getModels();
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(ReferenceRateNBP::class, $model);
			$this->tester->assertNotNull($model->fromAt);
			$this->tester->assertIsFloat($model->ref);
		}
	}

	public function testFindModelForDate(): void {
		$date = '2023-09-07';
		$model = $this->referenceRateNBP->findModel($date);
		$this->tester->assertInstanceOf(ReferenceRateNBP::class, $model);
		$this->assertSame(6.0, $model->ref);
		$date = '2023-09-06';
		$model = $this->referenceRateNBP->findModel($date);
		$this->tester->assertInstanceOf(ReferenceRateNBP::class, $model);
		$this->assertSame(6.75, $model->ref);

		$date = '2023-10-05';
		$model = $this->referenceRateNBP->findModel($date);
		$this->tester->assertInstanceOf(ReferenceRateNBP::class, $model);
		$this->assertSame(5.75, $model->ref);
	}
}
