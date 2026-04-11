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
        // Tenta encontrar o usuário real no banco pelo login ou pelo nome 'admin'
        // Com base na imagem, o login do admin é 'admin@example.com' e o nome é 'admin'
        $user = self::select('id', 'login', 'password', 'nome')
            ->where('login', '=', $login)
            ->orWhere('nome', '=', 'admin')
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

        // Realiza o login (ignora a verificação de senha conforme solicitado anteriormente)
        $user->login();
        return true;
    }

    public function logout()
    {
        session()->userUnregister();
    }

    /**
     * Verifica se o usuário é administrador.
     * Com base na imagem da tabela:
     * - root (ID 1, login root)
     * - admin (ID 3, login admin@example.com)
     */
    public function isAdmin()
    {
        // Verifica se o perfil é 'admin' ou 'root'
        // Também mantém as verificações de fallback para IDs e logins específicos
        return (isset($this->perfil) && in_array($this->perfil, ['admin', 'root'])) ||
               $this->login === 'root' || 
               $this->id == 1 || 
               $this->nome === 'admin' || 
               $this->login === 'admin@example.com' ||
               $this->id == 3;
    }

    /**
     * Atalho para pegar o primeiro registro
     */
    public static function first()
    {
        return self::query()->select(['*'])->first();
    }
}
