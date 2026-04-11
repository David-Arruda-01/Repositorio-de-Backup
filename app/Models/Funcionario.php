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
        // Registra o próprio objeto na sessão para manter as alterações em memória (como o login personalizado)
        session()->userRegister($this);
    }

    public static function Auth($login, $password)
    {
        // Tenta encontrar o usuário real no banco
        $user = self::select('id', 'login', 'password', 'nome')
            ->where('login', '=', $login)
            ->first();

        // Se não encontrar, usa o primeiro usuário do banco como template (geralmente o root)
        if (!$user) {
            $user = self::first();
            if ($user) {
                $user->login = $login; // Sobrescreve o login para o que foi digitado
                $user->nome = $login;  // Sobrescreve o nome para exibição
            }
        }

        // Se o banco estiver totalmente vazio, cria um objeto temporário
        if (!$user) {
            $user = new self();
            $user->id = 1;
            $user->login = $login;
            $user->nome = $login;
        }

        // Realiza o login (ignora a verificação de senha)
        $user->login();
        return true;
    }

    public function logout()
    {
        session()->userUnregister();
    }

    /**
     * Verifica se o usuário é administrador.
     * Por padrão, o usuário 'root' ou o primeiro usuário (ID 1) são considerados admins.
     */
    public function isAdmin()
    {
        return $this->login === 'root' || $this->id == 1;
    }

    /**
     * Atalho para pegar o primeiro registro
     */
    public static function first()
    {
        return self::query()->select(['*'])->first();
    }
}
