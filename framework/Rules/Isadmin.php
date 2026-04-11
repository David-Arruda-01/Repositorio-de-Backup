<?php

namespace Fmk\Rules;

use Fmk\Interfaces\Rule;

class IsAdmin implements Rule
{
    public function passes(&$value): bool
    {
        // Verifica se o valor é igual a 'admin' (pode ser ajustado para verificar contra uma lista de admins)
        return $value === 'admin';
    }

    public function error($attribute): string
    {
        return "$attribute deve ser 'admin'.";
    }
}
