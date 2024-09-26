<?php

namespace common\components\rbac;

interface ModelRbacInterface {

	public function getRbacName(): string;

	public function getRbacId(): ?string;

	public function getModelRbac(): ModelAccess;
}
