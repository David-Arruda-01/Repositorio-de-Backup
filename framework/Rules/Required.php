<?php

namespace Fmk\Rules;

use Fmk\Interfaces\Rule;

class Required implements Rule{
    public function passes(&$value):bool{
        return isset($value) && (trim($value) !== "");
    }

    public function error($attribute):string{
        return "$attribute não pode ficar em branco.";
    }


}