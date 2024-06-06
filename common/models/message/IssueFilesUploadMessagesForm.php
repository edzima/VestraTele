<?php

namespace common\models\message;

use common\components\message\MessageTemplate;
use common\helpers\Html;
use common\modules\file\models\File;
use common\modules\file\models\FileType;

class IssueFilesUploadMessagesForm extends IssueMessagesForm {

	/**
	 * @var File[]
	 */
	protected array $files;

	public string $fileUploader;

	public ?bool $sendSmsToCustomer = false;
	public ?bool $sendEmailToCustomer = false;
	public ?bool $sendSmsToAgent = false;
	private FileType $fileType;

	protected static function mainKeys(): array {
		return [
			'issue',
			'file',
			'upload',
		];
	}

	public function setFiles(array $files): void {
		$this->files = $files;
	}

	public function setFileType(FileType $fileType) {
		$this->fileType = $fileType;
	}

	public function getFileType(): FileType {
		return $this->fileType;
	}

	protected function parseTemplate(MessageTemplate $template): void {
		parent::parseTemplate($template);
		$this->parseFiles($template);
	}

	protected function parseFiles(MessageTemplate $template) {
		$count = count($this->files);
		$items = [];
		foreach ($this->files as $file) {
			$items[] = Html::issueFileLink($file, $this->issue->getIssueId(), true);
		}
		$template->parseSubject([
			'filesCount' => $count,
			'fileUploader' => $this->fileUploader,
		]);
		$template->parseBody([
			'filesCount' => $count,
			'fileUploader' => $this->fileUploader,
			'filesLinks' => Html::ul($items, [
				'encode' => false,
			]),
		]);
	}

}
