<?php

namespace common\models\message;

class Message extends \yii\swiftmailer\Message {

	private $_htmlBody;
	private $_textBody;

	public function setHtmlBody($html) {
		$this->_htmlBody = $html;
		parent::setHtmlBody($html);
	}

	public function setTextBody($text) {
		$this->_textBody = $text;
		parent::setTextBody($text);
	}

	/**
	 * Returns text of message
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->_htmlBody;
	}

	/**
	 * @return mixed
	 */
	public function getHtmlBody() {
		return $this->_htmlBody;
	}

	/**
	 * @return mixed
	 */
	public function getTextBody() {
		return $this->_textBody;
	}

}
