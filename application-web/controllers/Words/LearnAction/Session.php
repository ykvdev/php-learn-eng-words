<?php

namespace Web\Controllers\Words\LearnAction;

use Web\Entity\Word;

class Session {
    const ACTION_NAMESPACE = 'wordsLearn';

    private static $wordIdKey = 'wordIdKey';
    private static $noProgressKey = 'noProgress';

    /**
     * @param Word $value
     */
    static function setWordEntity(Word $value) {
        self::setValue(self::$wordIdKey, $value->id);
    }

    /**
     * @return Word
     */
    static function getWordEntity() {
        return Word::find(self::getValue(self::$wordIdKey));
    }

    static function setNoProgress() {
        self::setValue(self::$noProgressKey, true);
    }

    /**
     * @return bool
     */
    static function isNoProgress() {
        return self::getValue(self::$noProgressKey);
    }

    static function destroy() {
        unset($_SESSION[self::ACTION_NAMESPACE], $_POST);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    static function setValue($key, $value) {
        $_SESSION[self::ACTION_NAMESPACE][$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    static function getValue($key) {
        $_SESSION[self::ACTION_NAMESPACE] = isset($_SESSION[self::ACTION_NAMESPACE])
            ? $_SESSION[self::ACTION_NAMESPACE] : [];

        return isset($_SESSION[self::ACTION_NAMESPACE][$key])
            ? $_SESSION[self::ACTION_NAMESPACE][$key] : null;
    }
}