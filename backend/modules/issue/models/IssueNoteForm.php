<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-08
 * Time: 11:58
 */

namespace backend\modules\issue\models;

use common\models\issue\IssueNote;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class IssueNoteForm
 *
 * @property IssueNote $note
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class IssueNoteForm extends Model {

	public $title;
	public $description;

	/** @var IssueNote */
	private $note;

	public function __construct(IssueNote $note, array $config = []) {
		if ($note->issue === null) {
			throw  new InvalidConfigException('Issue must exist');
		}
		if ($note->user === null) {
			throw  new InvalidConfigException('User must exist');
		}
		$this->setNote($note);

		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[['title', 'description'], 'required'],
			[['title'], 'string', 'max' => 255],
		];
	}

	public function attributeLabels(): array {
		return $this->note->attributeLabels();
	}

	private function setNote(IssueNote $note): void {
		$this->note = $note;
		$this->title = $note->title;
		$this->description = $note->description;
	}

	public function getNote(): IssueNote {
		return $this->note;
	}

	public function save(): bool {
		if ($this->validate()) {
			$model = $this->getNote();
			$model->description = $this->description;
			$model->title = $this->title;
			return $model->save();
		}
		return false;
	}

}