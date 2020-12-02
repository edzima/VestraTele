<?php

namespace backend\tests\Page\provision;

use backend\tests\Step\acceptance\Admin;

class ProvisionTypePage {

	protected Admin $tester;

	public function __construct(Admin $I) {
		$this->tester = $I;
	}

	public function fillRequiredFields(string $name, string $value, bool $isPercentage = true): void {
		$I = $this->tester;
		$I->fillField('Name', $name);
		$I->fillField('Provision value', $value);
		if ($isPercentage) {
			$I->checkOption('Is percentage');
		} else {
			$I->uncheckOption('Is percentage');
		}
	}

	public function fillRole(string $role): void {
		$this->tester->fillOutSelect2OptionField('.field-provisiontypeform-roles', $role);
	}

}
