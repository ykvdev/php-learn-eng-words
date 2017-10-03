<?php

namespace Web\Controllers\Words\LearnAction;

use Web\Entity\Setting;
use Web\Entity\Word;

class MouseTypeModeAdapter extends AbstractModeAdapter {
    protected function run() {
        $this->initWordLettersIfNeed();
        $this->initViewVars();

        if($this->getPostParams('answer_letter') !== null) {
            $this->checkAnswer();
            if(count(Session::getValue('mouseTypeTaskEnLetters')) == 0) {
                $this->completeMode();
            }
        }
    }

    private function initWordLettersIfNeed() {
        if(Session::getValue('mouseTypeTaskEnLetters')) {
            return;
        }

        $letters = str_split(Session::getWordEntity()->en);
        shuffle($letters);
        Session::setValue('mouseTypeTaskEnLetters', $letters);
    }

    private function initViewVars() {
        $this->view->taskViewAlias = 'pages/words/learn/tasks/mouse-type';
        $this->view->ru = Session::getWordEntity()->ru;
        $this->view->enLetters = Session::getValue('mouseTypeTaskEnLetters');

        $typedEnWordLength = strlen(Session::getWordEntity()->en) - count(Session::getValue('mouseTypeTaskEnLetters'));
        $this->view->typedEnWord = substr(Session::getWordEntity()->en, 0, $typedEnWordLength);
    }

    private function checkAnswer() {
        $letters = str_split(Session::getWordEntity()->en);
        $nextLetterIndex = strlen(Session::getWordEntity()->en) - count(Session::getValue('mouseTypeTaskEnLetters'));
        $nextLetter = $letters[$nextLetterIndex];
        if($nextLetter != $this->getPostParams('answer_letter')) {
            $this->badAnswer();
        } else {
            $removeKey = array_search($this->getPostParams('answer_letter'), Session::getValue('mouseTypeTaskEnLetters'));
            unset($_SESSION[Session::ACTION_NAMESPACE]['mouseTypeTaskEnLetters'][$removeKey]);

            $this->view->enLetters = Session::getValue('mouseTypeTaskEnLetters');

            $typedEnWordLength = strlen(Session::getWordEntity()->en) - count(Session::getValue('mouseTypeTaskEnLetters'));
            $this->view->typedEnWord = substr(Session::getWordEntity()->en, 0, $typedEnWordLength);
        }
    }
}