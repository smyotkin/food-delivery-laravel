<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\DB;

class UniqueEmptyString implements ImplicitRule
{
    private $table;
    private $column;

    /**
     * Create a new rule instance.
     *
     * @param $table
     * @param $column
     */
    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
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
        return $value == '' ? (DB::table($this->table)->where($this->column, '=', '')->count() === 0) : true;
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
