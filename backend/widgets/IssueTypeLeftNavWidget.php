<?php

namespace backend\widgets;

use common\modules\issue\widgets\IssueTypeNav;

class IssueTypeLeftNavWidget extends IssueTypeNav {

	public bool $withFavorite = false;

	public ?string $childsClass = 'treeview';

}
