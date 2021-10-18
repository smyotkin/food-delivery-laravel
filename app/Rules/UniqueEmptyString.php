<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\DB;

class UniqueEmptyString implements ImplicitRule
{
    private $table;
    private $column;
    private $id;

    /**
     * Create a new rule instance.
     *
     * @param $table
     * @param $column
     */
    public function __construct($table, $column, $id)
    {
        $this->table = $table;
        $this->column = $column;
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value == '') {
            return DB::table($this->table)
                    ->where($this->column, '=', '')
                    ->where('id', '<>', $this->id)
                    ->count() === 0;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Такое значение поля :attribute уже существует.';
    }
}
