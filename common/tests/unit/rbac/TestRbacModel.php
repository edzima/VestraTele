<?php

namespace common\tests\unit\rbac;

use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;

class TestRbacModel implements ModelRbacInterface {

	private ?string $id = null;

	private string $baseName;

	public function __construct(string $baseName = 'test-rbac', string $id = null) {
		$this->baseName = $baseName;
		$this->id = $id;
	}

	public function setId(?string $id) {
		$this->id = $id;
	}

	public function getRbacId(): ?string {
		return $this->id;
	}

	public function getRbacBaseName(): string {
		return $this->baseName;
	}

	public function getModelRbac(): ModelAccessManager {
		// TODO: Implement getModelRbac() method.
	}
}
