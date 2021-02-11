<?php

namespace backend\tests\unit\provision;

use backend\modules\provision\models\ProvisionForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\models\provision\Provision;
use Decimal\Decimal;

class ProvisionFormTest extends Unit {

	private ProvisionForm $form;

	public function _before() {
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(),
				ProvisionFixtureHelper::provision()
			)

		);
		parent::_before();
	}

	public function testEmptyPercent(): void {
		$this->givenForm($this->grabProvision('nowak-self-payed'));

		$this->tester->assertSame($this->form->percent, 50.0);
		$this->form->percent = null;
		$this->thenUnsuccessSave();

		$this->thenSeeValidationError('Provision (%) cannot be blank.', 'percent');
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
		codecept_debug(Provision::find()->asArray()->all());
		$attributes = $this->form->getModel()->getAttributes();
		if ($value !== null) {
			$attributes['value'] = $value->toFixed(2);
		}
		$this->tester->seeRecord(Provision::class, $attributes);
	}

	private function thenUnsuccessSave(): void {
		$this->tester->assertFalse($this->form->save());
	}

	private function thenSuccessSave(): void {
		$this->tester->assertTrue($this->form->save());
	}

	private function thenSeeValidationError(string $message, string $attribute): void {
		$this->tester->assertSame($message, $this->form->getFirstError($attribute));
	}

	private function grabProvision(string $index): Provision {
		return $this->tester->grabFixture(ProvisionFixtureHelper::PROVISION, $index);
	}

	private function givenForm(Provision $provision): void {
		$this->form = new ProvisionForm($provision);
	}
}
