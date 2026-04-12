# Relatório de Correção do Método `getConfig`

## 1. Introdução

Este relatório detalha a análise e as correções implementadas no repositório `David-Arruda-01/Repositorio-de-Backup` para resolver um problema no método `getConfig` do arquivo `app/Models/Config.php`. O objetivo era garantir que este método carregasse as configurações corretamente, utilizando a classe `Fmk\Utils\Config` para configurações baseadas em arquivo e mesclando-as com as configurações armazenadas no banco de dados.

## 2. O que o `getConfig` deveria fazer

O método `getConfig` na classe `App\Models\Config` (localizada em `app/Models/Config.php`) é responsável por fornecer as configurações do sistema. Idealmente, ele deveria:

*   **Carregar configurações de arquivos**: Utilizar o utilitário `Fmk\Utils\Config` para ler configurações definidas em arquivos PHP (como `app/Configs/app.php`), que geralmente contêm configurações padrão ou de ambiente.
*   **Carregar configurações do banco de dados**: Obter configurações dinâmicas ou específicas do usuário armazenadas em uma tabela de banco de dados (presumivelmente `configs`).
*   **Mesclar e priorizar**: Combinar as configurações de ambas as fontes, dando prioridade às configurações do banco de dados para permitir sobrescritas e personalizações.
*   **`getValue`**: O método `getValue` deveria ser capaz de buscar uma configuração específica, primeiro no banco de dados e, se não encontrada, nos arquivos de configuração.

## 3. Qual era o problema

Foram identificados três problemas principais que impediam o correto funcionamento do sistema de configuração:

### 3.1. Problema no método `all()` em `app/Models/Config.php`

O método `all()` na classe `App\Models\Config` apresentava um erro lógico. Ele tentava iterar sobre os resultados de `parent::all()` e, para cada item (`$const`), tentava instanciar um novo objeto com `new $const;`. No entanto, `$const` já era um objeto (ou array associativo) representando uma linha da tabela de configurações, e não um nome de classe. Isso resultava em um erro fatal ou em um comportamento inesperado, pois a criação de um novo objeto dessa forma era inválida e redundante, já que `parent::all()` já retornava os objetos de configuração desejados.

### 3.2. `getConfig()` não utilizava `Fmk\Utils\Config`

O método `getConfig()` original em `app/Models/Config.php` apenas carregava as configurações diretamente do banco de dados. Ele não fazia uso da classe `Fmk\Utils\Config` para carregar configurações de arquivos, o que limitava a flexibilidade do sistema de configuração e não atendia à necessidade de mesclar configurações de diferentes fontes.

### 3.3. Redundância em `app/application.php`

O arquivo `app/application.php` continha duas chamadas consecutivas para `Initialize::createConstants`:

```php
Initialize::createConstants(include "Configs/app.php");
Initialize::createConstants(App\Models\Config::getConfig());
```

A primeira linha carregava as constantes diretamente do arquivo `Configs/app.php`, e a segunda tentava carregar as constantes do banco de dados. Com a modificação proposta para `App\Models\Config::getConfig()`, que agora mescla ambas as fontes, a primeira chamada se tornaria redundante e poderia causar conflitos ou comportamentos inesperados se as constantes fossem definidas duas vezes.

## 4. O que foi alterado e em quais arquivos

As seguintes alterações foram realizadas nos arquivos `app/Models/Config.php` e `app/application.php`:

### 4.1. `app/Models/Config.php`

#### 4.1.1. Correção do método `all()`

O método `all()` foi simplificado para retornar diretamente o resultado de `parent::all()`, removendo a lógica incorreta de re-instanciação de objetos.

**Antes:**
```php
    public static function all()
    {
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
```

**Depois:**
```php
    public static function all()
    {
        return parent::all();
    }
```

#### 4.1.2. Modificação do método `getConfig()`

O método `getConfig()` foi reescrito para primeiro carregar as configurações do arquivo `app.php` usando `Fmk\Utils\Config::getConfig('app')` e, em seguida, mesclá-las com as configurações do banco de dados. As configurações do banco de dados agora têm prioridade, sobrescrevendo quaisquer valores duplicados dos arquivos.

**Antes:**
```php
    public static function getConfig()
    {
        $all = self::all();
        $result = [];
        foreach ($all as $config) {
            $result[$config->name] = $config->value;
        }
        return $result;
    }
```

**Depois:**
```php
    public static function getConfig()
    {
        // Carrega configurações do arquivo framework/app/Configs/app.php usando o utilitário Fmk
        $fileConfigs = \Fmk\Utils\Config::getConfig("app");
        
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
```

#### 4.1.3. Modificação do método `getValue()`

O método `getValue()` foi aprimorado para primeiro tentar buscar a configuração no banco de dados. Se não for encontrada, ele tenta buscar a configuração nos arquivos usando `Fmk\Utils\Config::getConfig("app.$name")`, aproveitando a capacidade do utilitário de acessar valores aninhados usando a notação de ponto. Um bloco `try-catch` foi adicionado para lidar com possíveis exceções caso o arquivo de configuração não exista ou haja um erro ao carregá-lo.

**Antes:**
```php
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
```

**Depois:**
```php
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
            $fileValue = \Fmk\Utils\Config::getConfig("app.$name");
            if ($fileValue !== null) {
                return $fileValue;
            }
        } catch (\Exception $e) {
            // Arquivo não existe ou erro ao carregar, ignora e usa o default
        }

        return $default;
    }
```

### 4.2. `app/application.php`

#### 4.2.1. Remoção de redundância

A chamada redundante para `Initialize::createConstants(include "Configs/app.php")` foi removida, pois o método `App\Models\Config::getConfig()` agora é responsável por carregar e mesclar as configurações de arquivo e banco de dados de forma unificada.

**Antes:**
```php
Initialize::run();
Initialize::createConstants(include "Configs/app.php");
Initialize::createConstants(App\Models\Config::getConfig());
//A algum  proble com o Fmk\Utils\Config, por isso o getConfig não funciona...
```

**Depois:**
```php
Initialize::run();
// Carrega configurações (arquivo + banco de dados) e cria constantes
Initialize::createConstants(App\Models\Config::getConfig());
```

## 5. Conclusão

As alterações implementadas corrigem os problemas identificados no sistema de configuração, garantindo que o método `getConfig` em `app/Models/Config.php` funcione conforme o esperado. Agora, as configurações são carregadas de forma robusta, mesclando dados de arquivos e do banco de dados, com a devida prioridade para as configurações do banco de dados. A remoção da redundância em `app/application.php` também contribui para um código mais limpo e eficiente.

Com estas correções, o sistema de configuração do projeto deve operar de maneira mais confiável e flexível.
