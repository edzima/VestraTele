<?php

namespace common\modules\file\components;

use common\models\user\Worker;
use common\modules\file\models\File;
use common\modules\file\models\FileAccess;
use common\modules\file\models\FileType;
use yii\base\Component;
use yii\di\Instance;
use yii\rbac\ManagerInterface;

/**
 * @property ManagerInterface $manager
 */
class FileAuth extends Component {

	public array $allowedRoles = [
		Worker::ROLE_ISSUE_FILE_MANAGER,
	];
	public array $disallowedRoles = [];

	/**
	 * @var string|array|ManagerInterface
	 */
	public $authManager = 'authManager';

	protected File $file;

	protected int $userId;
	protected FileType $fileType;
	private array $checkRoles = [];

	public function init() {
		$this->authManager = Instance::ensure($this->authManager, ManagerInterface::class);
		parent::init();
	}

	/**
	 * @param int $userId
	 * @param File[] $files
	 * @return File[]
	 */
	public function filterUserFiles(int $userId, array $files, array $roles = []): array {
		return array_filter($files, function (File $file) use ($userId, $roles) {
			return $this->isForUser($userId, $file, $roles);
		});
	}

	public function isForUser(int $userId, File $file, array $roles = []): bool {
		return $this
			->setFile($file)
			->setUserId($userId)
			->setRoles($roles)
			->hasAccess();
	}

	public function isTypeForUser(int $userId, FileType $fileType, array $roles = []): bool {
		$this->setUserId($userId);
		$this->setFileType($fileType);
		$this->setRoles($roles);
		return $this->getFileType()->isPublic()
			|| ($this->userHasAllowedRole() && !$this->userHasDisallowedRoles());
	}

	public function hasAccess(): bool {
		return $this->isFileTypePublic()
			|| $this->userIsOwner()
			|| $this->userHasDirectlyAccessToFile()
			|| ($this->userHasAllowedRole() && !$this->userHasDisallowedRoles());
	}

	public function setUserId(int $userId): static {
		$this->userId = $userId;
		return $this;
	}

	public function setFile(File $file): static {
		$this->file = $file;
		$this->setFileType($file->fileType);
		return $this;
	}

	public function setFileType(FileType $fileType): static {
		$this->fileType = $fileType;
		return $this;
	}

	protected function isFileTypePublic(): bool {
		return $this->getFileType()->isPublic();
	}

	private function userIsOwner(): bool {
		return $this->file->owner_id === $this->userId;
	}

	protected function userHasDirectlyAccessToFile(): bool {
		return FileAccess::userHasAccess($this->userId, $this->getFileType()->id);
	}

	protected function userHasAllowedRole(): bool {
		return $this->checkAccess($this->allowedRoles, false) || $this->checkAccess($this->getAllowedTypeRoles(), true);
	}

	protected function userHasDisallowedRoles(): bool {
		return $this->checkAccess($this->disallowedRoles, false) || $this->checkAccess($this->getDisallowedTypeRoles(), true);
	}

	private function checkAccess(array $roles, bool $directly): bool {
		if ($directly && !empty($this->checkRoles)) {
			foreach ($roles as $role) {
				if (in_array($role, $this->checkRoles)) {
					return true;
				}
			}
			return false;
		}

		foreach ($roles as $role) {
			if ($this->authManager->checkAccess($this->userId, $role)) {
				return true;
			}
		}
		return false;
	}

	protected function getAllowedTypeRoles(): array {
		return $this->getFileType()->getVisibilityOptions()->allowedRoles;
	}

	protected function getDisallowedTypeRoles(): array {
		return $this->getFileType()->getVisibilityOptions()->disallowedRoles;
	}

	protected function getFileType(): FileType {
		return $this->fileType;
	}

	private function setRoles(array $roles): static {
		$this->checkRoles = $roles;
		return $this;
	}

}
