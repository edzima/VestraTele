<?php

namespace common\modules\file\models;

interface AttachableModel {

//	public function getTypeFiles(int $typeId): array;
//
//	public function getFiles(): array;

	public function linkFile(File $file);

	public function getDirParts(): array;

}
