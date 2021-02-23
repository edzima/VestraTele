<?php

namespace backend\tests\unit\provision;

use backend\modules\provision\models\ProvisionForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\provision\Provision;
use common\tests\_support\UnitModelTrait;
use Decimal\Decimal;
use yii\base\Model;

class ProvisionFormTest extends Unit {

	private ProvisionForm $form;

	use UnitModelTrait;

	public function _before() {
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(),
				ProvisionFixtureHelper::all()

			)

		);
		parent::_before();
	}

	public function testEmptyPercent(): void {
		$this->givenForm($this->grabProvision('nowak-self-payed'));

		$this->tester->assertSame($this->form->percent, '50.00');
		$this->form->percent = null;
		$this->thenUnsuccessSave();

		$this->thenSeeError('Provision (%) cannot be blank.', 'percent');
	}

	public function testNotChangePercent(): void {
		$this->givenForm($this->grabProvision('nowak-self-payed'));
		$this->thenSuccessSave();
	}

	public function testChangePercent(): void {
		$this->givenForm($this->grabProvision('nowak-self-payed'));

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
		return $this->tester->grabFixture(ProvisionFixtureHelper::PROVISION, $index);
	}

	private function givenForm(Provision $provision): void {
		$this->form = new ProvisionForm($provision);
	}

	public function getModel(): Model {
		return $this->form;
	}
}
