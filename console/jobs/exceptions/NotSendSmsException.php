<?php

namespace console\jobs\exceptions;

class NotSendSmsException extends Exception {

	/**
	 * @return string the user-friendly name of this exception
	 */
	public function getName(): string {
		return 'Not Successfully Send SMS in Job.';
	}
}
