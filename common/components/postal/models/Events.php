<?php

namespace common\components\postal\models;

class Events {

	public string $czas;
	public Entity $jednostka;
	public string $kod;
	public bool $konczace;
	public string $nazwa;

	public ?Cause $przyczyna;
}
