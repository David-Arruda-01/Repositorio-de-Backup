<?php

namespace App\Models;

use Fmk\MVC\Model;
use Fmk\Database\DB;

class Config extends Model {
/*
    public static function all(){
        $all = parent::all();
        $result = [];
        foreach($all as $const){
            $result[$const->name] = $const->value;
        }
        return $result;
    }
*/
    public static function all() {
        $all = parent::all();
        $result = [];
        foreach ($all as $const) {
            // Criando um objeto padrão
            $obj = new $const; 
            $obj->id = $const->id;
            $obj->name = $const->name;
            $obj->label = $const->label;
            $obj->value = $const->value;
            $result[] = $obj;
        }
        return $result;
    }

    public static function getConfig(){
        $all = self::all();
        $result = [];
        foreach($all as $config){
            $result[$config->name] = $config->value;
        }
        return $result;
    }

     /**
     * Retorna o valor de uma configuração pelo nome.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $name, $default = null)
    {
        $config = DB::query('configs')
            ->where('name', $name)
            ->first(); // retorna o registro completo

        if (!$config) {
            return $default;
        }

        return $config->value ?? $default;
    }
}
