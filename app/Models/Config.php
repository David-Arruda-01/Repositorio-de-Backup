<?php

namespace App\Models;

use Fmk\MVC\Model;
use Fmk\Database\DB;
use Fmk\Utils\Config as FmkConfig;

class Config extends Model
{
    /**
     * Carrega as configurações mesclando dados do arquivo 'app.php' 
     * com as configurações armazenadas no banco de dados.
     *
     * @return array
     */
    public static function getConfig()
    {
        // Carrega configurações do arquivo framework/app/Configs/app.php usando o utilitário Fmk
        $fileConfigs = FmkConfig::getConfig('app');
        
        if (!is_array($fileConfigs)) {
            $fileConfigs = [];
        }

        $dbConfigs = [];
        $all = self::all();
        
        if (is_array($all)) {
            foreach ($all as $config) {
                if (isset($config->name) && isset($config->value)) {
                    $dbConfigs[$config->name] = $config->value;
                }
            }
        }

        // Mescla as configurações, dando prioridade às do banco de dados (sobrescreve arquivo)
        return array_merge($fileConfigs, $dbConfigs);
    }

    /**
     * Retorna o valor de uma configuração pelo nome.
     * Tenta buscar no banco de dados primeiro, depois no arquivo de configuração.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $name, $default = null)
    {
        // 1. Tenta buscar no banco de dados usando DB::query conforme padrão do framework
        $config = DB::query('configs')
            ->where('name', $name)
            ->first();

        if ($config && isset($config->value)) {
            return $config->value;
        }

        // 2. Se não encontrar no banco, tenta buscar nos arquivos de configuração via Fmk\Utils\Config
        try {
            // O utilitário Config permite filtrar usando pontos (ex: app.N_MESAS)
            $fileValue = FmkConfig::getConfig("app.$name");
            if ($fileValue !== null) {
                return $fileValue;
            }
        } catch (\Exception $e) {
            // Arquivo não existe ou erro ao carregar, ignora e usa o default
        }

        return $default;
    }
}
