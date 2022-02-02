<?php

namespace backend\tests\unit\provision;

use backend\modules\provision\models\ProvisionUpdateForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\provision\Provision;
use common\tests\_support\UnitModelTrait;
use Decimal\Decimal;
use yii\base\InvalidConfigException;
use yii\base\Model;

class ProvisionUpdateFormTest extends Unit {

	private ProvisionUpdateForm $form;
	private ProvisionFixtureHelper $provisionFixture;

	use UnitModelTrait;

	public function _before() {
		$this->provisionFixture = new ProvisionFixtureHelper($this->tester);
		$this->tester->haveFixtures(
			array_merge(
				ProvisionFixtureHelper::provision(),
				ProvisionFixtureHelper::type(),
				SettlementFixtureHelper::pay(),
				SettlementFixtureHelper::settlement(),
			)
		);
		parent::_before();
	}

	public function testNewRecord(): void {
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->givenForm(new Provision());
		});
	}

	public function testEmptyPercent(): void {
		$this->givenForm($this->grabProvision('nowak-self-paid'));

		$this->tester->assertSame($this->form->percent, '50.00');
		$this->form->percent = null;
		$this->thenUnsuccessValidate();

		$this->thenSeeError('Provision (%) cannot be blank.', 'percent');
	}

	public function testNotChangePercent(): void {
		$this->givenForm($this->grabProvision('nowak-self-paid'));
		$this->thenSuccessSave();
	}

	public function testChangePercent(): void {
		$this->givenForm($this->provisionFixture->grabProvision('nowak-self-paid'));

		$this->form->percent = 60;

		$this->thenSuccessSave();
		$this->thenSeeProvision(new Decimal(600));
	}

	private function thenSeeProvision(Decimal $value = null): void {
		$attributes = $this->form->getModel()->getAttributes();
		if ($value !== null) {
			$attributes['value'] = $value->toFixed(2);
		}
		$this->tester->seeRecord(Provision::class, $attributes);
	}

	private function grabProvision(string $index): Provision {
		return $this->provisionFixture->grabProvision($index);
	}

	private function givenForm(Provision $provision): void {
		$this->form = new ProvisionUpdateForm($provision);
	}

	public function getModel(): Model {
		return $this->form;
	}
}
