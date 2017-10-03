<?php

namespace Web\Controllers\Words\LearnAction;

use Web\Entity\Setting;
use Web\Entity\Word;

class ShowModeAdapter extends AbstractModeAdapter {
    protected function run() {
        $this->setOnLearnWordState();
        $this->initViewVars();

        if($this->getPostParams('next')) {
            $this->completeMode();
        }
    }

    private function setOnLearnWordState() {
        $word = Session::getWordEntity();
        $word->state = Word\State::ON_LEARN;
        $word->save();
    }

    private function initViewVars() {
        $this->view->taskViewAlias = 'pages/words/learn/tasks/show';
        $this->view->en = Session::getWordEntity()->en;
        $this->view->ru = Session::getWordEntity()->ru;
    }
}