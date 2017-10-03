<?php

namespace Web\Entity;

use Lib\AbstractEntity;

class Setting extends AbstractEntity {
    protected $tableName = 'settings';

    protected $primaryKey = 'key';

    /** @var string */
    public $key;

    /** @var string */
    public $value;
}