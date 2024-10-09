<?php

namespace common\components\rbac;

interface ModelRbacInterface {

	public function getRbacBaseName(): string;

	public function getRbacId(): ?string;

	public function getModelRbac(): ModelAccessManager;
}
