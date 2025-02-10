<?php

namespace common\modules\court\components;

use common\modules\court\models\Lawsuit;
use common\modules\court\models\LawsuitSession;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitSessionDTO;
use Yii;
use yii\base\Component;

class LawsuitSessionsSync extends Component {

	/**
	 * @param Lawsuit $lawsuit
	 * @param LawsuitSessionDTO[] $spiSessions
	 * @return void
	 */
	public function sync(Lawsuit $lawsuit, array $spiSessions): int {
		$toCreate = [];
		$toCreateKeys = [];
		$count = 0;
		foreach ($spiSessions as $spiSession) {
			$same = array_filter($lawsuit->sessions, function (LawsuitSession $session) use ($spiSession) {
				return $this->isEqual($session, $spiSession);
			});
			if (empty($same)) {
				$attributes = $this->sessionAttribute($spiSession);
				$attributes['lawsuit_id'] = $lawsuit->id;
				$toCreate[] = $attributes;
				$toCreateKeys = array_keys($attributes);
			} else {
				$first = array_shift($same);
				if ($first->updateAttributes($this->sessionAttribute($spiSession))) {
					$count++;
				}
				$details = [];
				foreach ($same as $session) {
					$details[] = $session->details;
					Yii::warning('Duplicate session: ' . $session->date_at . ' in Lawsuit: ' . $lawsuit->id, __METHOD__);
					$session->delete();
				}
				if (!empty($details)) {
					array_unshift($details, $lawsuit->details);
					$lawsuit->updateAttributes([
						'details' => implode("\n", $details),
					]);
				}
			}
		}
		if (!empty($toCreate)) {
			$count += LawsuitSession::getDb()
				->createCommand()
				->batchInsert(LawsuitSession::tableName(),
					$toCreateKeys,
					$toCreate)
				->execute();
		}
		return $count;
	}

	protected function sessionAttribute(LawsuitSessionDTO $session): array {
		return [
			'room' => $session->room,
			'created_at' => date(DATE_ATOM, strtotime($session->createdDate)),
			'updated_at' => date(DATE_ATOM, strtotime($session->modificationDate)),
			'date_at' => date(DATE_ATOM, strtotime($session->date)),
			'result' => $session->result,
			'judge' => $session->judge,
		];
	}

	private function isEqual(LawsuitSession $session, LawsuitSessionDTO $spiSession): bool {
		if (strtotime($session->created_at) === strtotime($spiSession->createdDate)) {
			return true;
		}
		if (strtotime($session->date_at) === strtotime($spiSession->date)) {
			return true;
		}
		return false;
	}
}
