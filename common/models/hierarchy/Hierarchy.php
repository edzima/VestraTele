<?php

namespace common\models\hierarchy;

interface Hierarchy {

	public function getParentId(): ?int;

	public function getParentsIds(): array;

	public function getChildesIds(): array;

	public function getAllChildesIds(): array;
}
