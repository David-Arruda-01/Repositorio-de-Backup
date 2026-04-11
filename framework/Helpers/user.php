<?php

if(!function_exists('user')){
    function user(){
        return Fmk\Utils\Session::getInstance()->getUser();
    }
}

if(!function_exists('isAdmin')){
    /**
     * Verifica se o usuário logado é administrador.
     */
    function isAdmin(){
        $user = user();
        return $user && method_exists($user, 'isAdmin') && $user->isAdmin();
    }
}