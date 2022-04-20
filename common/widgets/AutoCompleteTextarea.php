<?php

namespace common\widgets;

use yii\helpers\Html;
use yii\jui\AutoComplete;

class AutoCompleteTextarea extends AutoComplete
{
    /**
     * Renders the AutoComplete widget.
     * @return string the rendering result.
     */
    public function renderWidget(): string
    {
        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            return Html::textarea($this->name, $this->value, $this->options);
        }
    }
}