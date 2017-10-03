<?php namespace Web\Controllers\Words;

use Lib\Controller\AbstractAction;
use Web\Controllers\Words\LearnAction\AbstractModeAdapter;
use Web\Controllers\Words\LearnAction\Session;
use Web\Entity\Setting;
use Web\Entity\Word;

class LearnAction extends AbstractAction
{
    /** @var AbstractModeAdapter */
    private $modeAdapter;

    public function run()
    {
        $this->initViewVars();
        $this->initRandWordIfNeed();
        $this->runModeAdapterIfNeed();
    }

    private function initViewVars() {
        $this->view->waitWordsCount = $this->getWaitWordsCount();
        $this->view->onLearnWordsCount = $this->getOnLearnWordsCount();
        $this->view->onRepeatWordsCount = $this->getOnRepeatWordsCount();
        $this->view->onRepeatTomorrowWordsCount = $this->getOnRepeatTomorrowWordsCount();
        $this->view->learnedWordsCount = $this->getLearnedWordsCount();
    }

    private function initRandWordIfNeed() {
        if(Session::getWordEntity()) {
            return;
        }

        $onLearnCount = $this->getOnLearnWordsCount();
        $onRepeatCount = $this->getOnRepeatWordsCount();
        $waitsCount = $this->getWaitWordsCount();

        if($onLearnCount == 0 && $onRepeatCount == 0 && $waitsCount == 0) {
            $this->view->taskViewAlias = 'pages/words/learn/learn-all-words-finished';
            return;
        }

        $learnWordsCount = Setting::find(Setting\Key::learn_words_number)->value;
        if($onLearnCount + $onRepeatCount < $learnWordsCount && $waitsCount > 0) {
            $this->initWaitRandWord();
        } elseif((rand(0, 1) && $onLearnCount > 0) || $onRepeatCount == 0) {
            $this->initOnLearnRandWord();
        } else {
            $this->initOnRepeatRandWord();
        }
    }

    private function runModeAdapterIfNeed()
    {
        if(!Session::getWordEntity()) {
            return;
        }

        $showWordNumber = Setting::find(Setting\Key::show_word_number)->value;
        $selectWordNumber = Setting::find(Setting\Key::select_word_number)->value;
        $mouseTypeNumber = Setting::find(Setting\Key::mouse_type_number)->value;
        $keyboardTypeNumber = Setting::find(Setting\Key::keyboard_type_number)->value;
        $keyboardTypeRepeatNumber = Setting::find(Setting\Key::keyboard_type_repeat_number)->value;

        if (Session::getWordEntity()->repeated >= 0
            && Session::getWordEntity()->repeated < $showWordNumber
        ) {
            $className = AbstractModeAdapter::SHOW_CLASS_NAME;
        }
        if (Session::getWordEntity()->repeated >= $showWordNumber
            && Session::getWordEntity()->repeated < $selectWordNumber
        ) {
            $className = AbstractModeAdapter::SELECT_CLASS_NAME;
        } elseif (Session::getWordEntity()->repeated >= $selectWordNumber
            && Session::getWordEntity()->repeated < $mouseTypeNumber
        ) {
            $className = AbstractModeAdapter::MOUSE_TYPE_CLASS_NAME;
        } elseif (Session::getWordEntity()->repeated >= $mouseTypeNumber
            && Session::getWordEntity()->repeated < $keyboardTypeNumber
        ) {
            $className = AbstractModeAdapter::KEYBOARD_TYPE_CLASS_NAME;
        } elseif (Session::getWordEntity()->repeated >= $keyboardTypeNumber
            && Session::getWordEntity()->repeated < $keyboardTypeRepeatNumber
        ) {
            $className = AbstractModeAdapter::KEYBOARD_TYPE_CLASS_NAME;
        }

        $className = __CLASS__ . '\\' . $className;
        $this->modeAdapter = new $className($this->view);
    }

    private function getOnLearnWordsCount() {
        $sql = '
        select count(id)
        from words
        where state = ?
        ';
        return (int)mysqldb()->fetchOne($sql, Word\State::ON_LEARN);
    }

    private function getOnRepeatWordsCount() {
        $yesterday = (new \DateTime())->modify('-1 day');
        $sql = '
        select count(id)
        from words
        where state = ?
        and on_repeat_at <= ?
        ';
        return (int)mysqldb()->fetchOne($sql, Word\State::ON_REPEAT, $yesterday->format('Y-m-d 23:59:59'));
    }

    private function getWaitWordsCount() {
        $sql = '
        select count(id)
        from words
        where state = ?
        ';
        return mysqldb()->fetchOne($sql, Word\State::WAIT);
    }

    private function initWaitRandWord() {
        $words = Word::findAll([
            'state' => Word\State::WAIT
        ]);
        Session::setWordEntity($words[array_rand($words)]);
    }

    private function initOnRepeatRandWord() {
        $yesterday = (new \DateTime())->modify('-1 day');
        $words = Word::findAll([
            'state = ?' => Word\State::ON_REPEAT,
            'on_repeat_at <= ?' => $yesterday->format('Y-m-d 23:59:59')
        ]);
        Session::setWordEntity($words[array_rand($words)]);
    }

    private function initOnLearnRandWord() {
        $words = Word::findAll([
            'state' => Word\State::ON_LEARN
        ]);
        Session::setWordEntity($words[array_rand($words)]);
    }

    private function getOnRepeatTomorrowWordsCount() {
        $sql = '
        select count(id)
        from words
        where state = ?
        and on_repeat_at >= ?
        ';
        return (int)mysqldb()->fetchOne($sql, Word\State::ON_REPEAT, date('Y-m-d 00:00:00'));
    }

    private function getLearnedWordsCount() {
        $sql = '
        select count(id)
        from words
        where state = ?
        ';
        return mysqldb()->fetchOne($sql, Word\State::LEARNED);
    }
}