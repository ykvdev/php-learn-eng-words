<?php

namespace Web\Controllers\Words\LearnAction;

use Web\Entity\Setting;
use Web\Entity\Word;

class SelectModeAdapter extends AbstractModeAdapter {
    protected function run() {
        $this->initWordsSetIfNeed();
        $this->initViewVars();

        if($this->getPostParams('answer')) {
            $this->checkAnswer();
            $this->completeMode();
        }
    }

    private function initWordsSetIfNeed() {
        if(Session::getValue('selectTaskWord')) {
            return;
        }

        $words = array_merge($this->getOnLearnWords(), $this->getOnRepeatWords());
        shuffle($words);
        if(rand(0, 1)) {
            Session::setValue('selectTaskWord', Session::getWordEntity()->en);

            $taskWords = [];
            for($i = 0; $i < 4; $i++) {
                if(!isset($words[$i])) {
                    continue;
                }

                $taskWords[] = $words[$i]->ru;
            }
            $taskWords[] = Session::getWordEntity()->ru;
            shuffle($taskWords);

            Session::setValue('selectTaskWords', $taskWords);
        } else {
            Session::setValue('selectTaskWord', Session::getWordEntity()->ru);

            $taskWords = [];
            for($i = 0; $i < 4; $i++) {
                if(!isset($words[$i])) {
                    continue;
                }

                $taskWords[] = $words[$i]->en;
            }
            $taskWords[] = Session::getWordEntity()->en;
            shuffle($taskWords);

            Session::setValue('selectTaskWords', $taskWords);
        }
    }

    private function getOnLearnWords() {
        $words = Word::findAll([
            'state = ?' => Word\State::ON_LEARN,
            'id != ?' => Session::getWordEntity()->id
        ]);
        return $words;
    }

    private function getOnRepeatWords() {
        $words = Word::findAll([
            'state = ?' => Word\State::ON_REPEAT,
            'id != ?' => Session::getWordEntity()->id
        ]);
        return $words;
    }

    private function initViewVars() {
        $this->view->taskViewAlias = 'pages/words/learn/tasks/select';
        $this->view->word = Session::getValue('selectTaskWord');
        $this->view->words = Session::getValue('selectTaskWords');
    }

    private function checkAnswer() {
        if($this->getPostParams('answer') != Session::getWordEntity()->en
        && $this->getPostParams('answer') != Session::getWordEntity()->ru) {
            $this->badAnswer();
        }
    }
}