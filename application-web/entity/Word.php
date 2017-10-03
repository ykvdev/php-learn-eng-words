<?php

namespace Web\Entity;

use Lib\AbstractEntity;

class Word extends AbstractEntity
{
    protected $tableName = 'words';

    protected $primaryKey = 'id';

    /** @var int */
    public $id;

    /** @var string */
    public $en;

    /** @var string */
    public $ru;

    /** @var int */
    public $repeated;

    /** @var string */
    public $state;

    /** @var string */
    public $on_repeat_at;
}