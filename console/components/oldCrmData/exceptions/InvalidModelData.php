<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-03
 * Time: 16:00
 */

namespace console\components\oldCrmData\exceptions;

use Throwable;
use yii\base\Model;

class InvalidModelData extends Exception {

	public function __construct(Model $model, array $data, int $code = 0, Throwable $previous = null) {
		$message = 'Model: ' . json_encode($model->attributes) . "\n";

		$message .= ' for data: ' . json_encode($data) . ".\n";
		$message .= ' Errors: ' . json_encode($model->getErrors()) . "\n";
		parent::__construct($message, $code, $previous);
	}
}