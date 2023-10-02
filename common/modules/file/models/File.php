<?php

namespace common\modules\file\models;

use common\models\issue\Issue;
use common\models\user\User;
use fredyns\attachments\helpers\AttachmentHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $name
 * @property string $hash
 * @property int $size
 * @property string $type
 * @property string $mime
 * @property int $file_type_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $owner_id
 * @property string $path
 *
 * @property FileAccess[] $fileAccesses
 * @property IssueFile[] $issueFiles
 * @property Issue[] $issues
 * @property User $owner
 * @property FileType $fileType
 */
class File extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%file}}';
	}

	public function isForUser(int $userId): bool {
		return $this->owner_id === $userId
			|| $this->fileType->isPublic()
			|| $this->hasAccess($userId);
	}

	public function getNameWithType(): string {
		return $this->name . '.' . $this->type;
	}

	public function getTypeName(): string {
		return FileType::getNames(false)[$this->file_type_id];
	}

	public static function compose(string $filePath): self {
		$file = new static();
		$file->generateName($filePath);
		$file->hash = md5(microtime(true) . $filePath);
		$file->size = filesize($filePath);
		$file->type = pathinfo($filePath, PATHINFO_EXTENSION);
		$file->mime = FileHelper::getMimeType($filePath);
		return $file;
	}

	public function generateName(string $filePath): void {
		$fileName = pathinfo($filePath, PATHINFO_FILENAME);
		$fileName = Inflector::slug($fileName);
		$this->name = $fileName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'hash', 'size', 'type', 'mime', 'file_type_id', 'owner_id'], 'required'],
			[['size', 'file_type_id', 'owner_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['name', 'hash', 'type', 'mime'], 'string', 'max' => 255],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
			[['file_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => FileType::class, 'targetAttribute' => ['file_type_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('file', 'ID'),
			'name' => Yii::t('file', 'Name'),
			'hash' => Yii::t('file', 'Hash'),
			'size' => Yii::t('file', 'Size'),
			'type' => Yii::t('file', 'Type'),
			'mime' => Yii::t('file', 'Mime'),
			'file_type_id' => Yii::t('file', 'File Type'),
			'typeName' => Yii::t('file', 'File Type'),
			'created_at' => Yii::t('file', 'Created At'),
			'updated_at' => Yii::t('file', 'Updated At'),
			'owner_id' => Yii::t('file', 'Owner ID'),
			'owner' => Yii::t('file', 'Owner'),
			'path' => Yii::t('file', 'Path'),
		];
	}

	/**
	 * Gets query for [[FileAccesses]].
	 *
	 * @return ActiveQuery
	 */
	public function getFileAccesses() {
		return $this->hasMany(FileAccess::class, ['user_id' => 'id']);
	}

	/**
	 * Gets query for [[IssueFiles]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssueFiles() {
		return $this->hasMany(IssueFile::class, ['file_id' => 'id']);
	}

	/**
	 * Gets query for [[Issues]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['id' => 'issue_id'])->viaTable('issue_file', ['file_id' => 'id']);
	}

	/**
	 * Gets query for [[Owner]].
	 *
	 * @return ActiveQuery
	 */
	public function getOwner() {
		return $this->hasOne(User::class, ['id' => 'owner_id']);
	}

	/**
	 * Gets query for [[FileType]].
	 *
	 * @return ActiveQuery
	 */
	public function getFileType() {
		return $this->hasOne(FileType::class, ['id' => 'file_type_id']);
	}

	public function getFileAccess() {
		return $this->hasMany(FileAccess::class, ['file_id' => 'id']);
	}

	private function hasAccess(int $userId): bool {
		return FileAccess::userHasAccess($userId, $this->id);
	}

}
