<?php

namespace App\Models;

use Fmk\Interfaces\Auth;
use Fmk\MVC\Model;

class Funcionario extends Model implements Auth
{
    protected $fillable = [
        'nome',
        'login',
        'telefone',
        'cpf',
        'rg',
        'rg_expedidor',
        'password'
    ];

    /**
     * 🔥 Sobrescreve o save para aceitar array
     */
    public function save(array $data = [])
    {
        // Preenche automaticamente os campos permitidos
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->$key = $value;
            }
        }

        // Chama o save original do Model
        return parent::save();
    }

    public function login()
    {
        session()->userRegister(Funcionario::find($this->id));
    }

    public static function Auth($login, $password)
    {
        $user = self::select('id', 'login', 'password')
            ->where('login', '=', $login)
            ->first();

        if ($user && password_verify($password, $user->password)) {
            $user->login();
            return true;
        }

        return false;
    }

    public function logout()
    {
        session()->userUnregister();
    }
}
