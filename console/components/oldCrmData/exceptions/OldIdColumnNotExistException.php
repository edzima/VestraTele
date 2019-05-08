<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-03
 * Time: 14:34
 */

namespace console\components\oldCrmData\exceptions;

use console\components\oldCrmData\Migration;
use Throwable;

class OldIdColumnNotExistException extends Exception {

	public function __construct(string $tableName, int $code = 0, Throwable $previous = null) {
		$message = Migration::OLD_ID_COLUMN_NAME . ' column not exist in ' . $tableName;
		parent::__construct($message, $code, $previous);
	}
}