<?php

namespace Web\Entity\Word;

class State {
    const WAIT = 'wait';
    const ON_LEARN = 'on_learn';
    const ON_REPEAT = 'on_repeat';
    const LEARNED = 'learned';
}