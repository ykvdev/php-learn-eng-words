<?php

namespace Web\Controllers\Words\LearnAction;

use Lib\Http\RequestTrait;
use Lib\ProcessException;
use Lib\ViewRenderer;
use Web\Controllers\Words\LearnAction\Session;
use Web\Entity\Setting;
use Web\Entity\Word;

abstract class AbstractModeAdapter {
    use RequestTrait;

    const SHOW_CLASS_NAME = 'ShowModeAdapter';
    const SELECT_CLASS_NAME = 'SelectModeAdapter';
    const MOUSE_TYPE_CLASS_NAME = 'MouseTypeModeAdapter';
    const KEYBOARD_TYPE_CLASS_NAME = 'KeyboardTypeModeAdapter';

    /** @var ViewRenderer */
    protected $view;

    /**
     * @param ViewRenderer $view
     */
    public function __construct(ViewRenderer $view) {
        $this->view = $view;

        $this->initWordProgress();
        $this->initHintIfNeed();
        $this->setWordAlreadyLearnedIfNeed();
        $this->run();
    }

    protected function initWordProgress() {
        $totalWordProgress = Setting::find(Setting\Key::keyboard_type_repeat_number)->value;
        $this->view->wordProgress = round(100 / $totalWordProgress * Session::getWordEntity()->repeated);
    }

    protected function initHintIfNeed() {
        if ($this->getPostParams('hint')) {
            $this->view->hintEn = Session::getWordEntity()->en;
            $this->view->hintRu = Session::getWordEntity()->ru;
            Session::setNoProgress();
        }
    }

    protected function setWordAlreadyLearnedIfNeed()
    {
        if ($this->getPostParams('already_learned')) {
            $word = Session::getWordEntity();
            $word->state = Word\State::LEARNED;
            $word->save();

            $this->completeMode();
        }
    }

    abstract protected function run();

    protected function badAnswer() {
        Session::setNoProgress();
        throw new ProcessException('Ответ не верный');
    }

    protected function completeMode() {
        if (!Session::isNoProgress()) {
            $word = Session::getWordEntity();
            $word->repeated++;
            $word->save();
        }

        if (Session::getWordEntity()->state == Word\State::ON_LEARN
        && Session::getWordEntity()->repeated == Setting::find(Setting\Key::keyboard_type_number)->value) {
            $word = Session::getWordEntity();
            $word->state = Word\State::ON_REPEAT;
            $word->on_repeat_at = date('Y-m-d H:i:s');
            $word->save();
        }

        if(Session::getWordEntity()->state == Word\State::ON_REPEAT
        && Session::isNoProgress()) {
            $word = Session::getWordEntity();
            $word->state = Word\State::ON_LEARN;
            $word->repeated = 0;
            $word->on_repeat_at = null;
            $word->save();
        }

        if (Session::getWordEntity()->state == Word\State::ON_REPEAT
        && Session::getWordEntity()->repeated == Setting::find(Setting\Key::keyboard_type_repeat_number)->value) {
            $word = Session::getWordEntity();
            $word->state = Word\State::LEARNED;
            $word->save();
        }

        Session::destroy();
        redirect('words-learn');
    }
}