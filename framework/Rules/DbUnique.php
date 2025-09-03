<?php
// namespace Fmk\Rules;

// use Fmk\Interfaces\Rule;
// use Fmk\Rules\DbExists; // Certifique-se de que a classe está sendo importada corretamente
// use Fmk\MVC\Model;

// class DbUnique implements Rule{
//     protected string $table;
//     protected string $column;
//     protected ?int $ignoreId;

//     public function __construct(string $table, string $column, ?int $ignoreId = null)
//     {
//         $this->table = $table;
//         $this->column = $column;
//         $this->ignoreId = $ignoreId;
//     }

//     public function passesData($attribute, $value): bool
//     {
//         // Verifica se o valor existe usando DbExists (caso a classe tenha um método estático)
//         if (DbExists::passes($value));

//         // Criando a query
//         $query = DbExists::table($this->table)->where($this->column, $value);

//         // Se um ID for ignorado (caso de atualização), excluí-lo da busca
//         if ($this->ignoreId !== null) {
//             $query->where('id', '!=', $this->ignoreId);
//         }

//         return !$query->exists();
//     }

//     public function message(): string
//     {
//         return 'O valor informado para :attribute já está em uso.';
//     }
// }