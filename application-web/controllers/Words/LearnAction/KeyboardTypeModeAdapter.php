<?php

namespace Web\Controllers\Words\LearnAction;

use Web\Entity\Setting;
use Web\Entity\Word;

class KeyboardTypeModeAdapter extends AbstractModeAdapter {
    protected function run() {
        $this->initViewVars();

        if($this->getPostParams('check')) {
            $this->checkAnswer();
            $this->completeMode();
        }
    }

    private function initViewVars() {
        $this->view->taskViewAlias = 'pages/words/learn/tasks/keyboard-type';
        $this->view->ru = Session::getWordEntity()->ru;
    }

    private function checkAnswer() {
        if(strnatcasecmp($this->getPostParams('answer'), Session::getWordEntity()->en) != 0) {
            $this->badAnswer();
        }
    }
}