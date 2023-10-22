<?php

namespace App\Models;


class SingleCharacter
{
    protected \stdClass $character;

    public function __construct(\stdClass $character)
    {
        $this->character = $character;
    }

    public function getCharacter(): \stdClass
    {
        return $this->character;
    }

}