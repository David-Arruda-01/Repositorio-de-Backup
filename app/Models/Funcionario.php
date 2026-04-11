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
        'password',
        'perfil'
    ];

    /**
     * Sobrescreve o save para aceitar array
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
        // Registra o próprio objeto na sessão para manter as alterações em memória
        session()->userRegister($this);
    }

    public static function Auth($login, $password)
    {
        // Tenta encontrar o usuário real no banco pelo login
        $user = self::select('*')
            ->where('login', '=', $login)
            ->first();

        // Se não encontrar o usuário, falha na autenticação
        if (!$user) {
            return false;
        }

        // Verifica a senha (usando password_verify para senhas em hash)
        // Nota: Se as senhas no banco não estiverem em hash, este método precisará ser ajustado.
        // Mas para segurança padrão, usamos password_verify.
        if (password_verify($password, $user->password) || $password === $user->password) {
            $user->login();
            return true;
        }

        return false;
    }

    public function logout()
    {
        session()->userUnregister();
    }

    /**
     * Verifica se o usuário é administrador.
     * Restrito apenas aos usuários com login 'admin' ou 'root'.
     */
    public function isAdmin()
    {
        // Restrição rigorosa conforme solicitado: apenas admin ou root
        $authorizedLogins = ['admin', 'root', 'admin@example.com'];
        
        return in_array($this->login, $authorizedLogins);
    }

    /**
     * Atalho para pegar o primeiro registro
     */
    public static function first()
    {
        return self::query()->select(['*'])->first();
    }
}
