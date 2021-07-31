<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UsersService;
use Illuminate\Console\Command;

class UserChangePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change' .
                            '{phone : Телефон пользователя}' .
                            '{--password= : Новый пароль пользователя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Редактирование данных пользователя по номеру телефона';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Выполнить консольную команду
     *
     */
    public function handle()
    {
        try {
            if ($user = UsersService::getByPhone($this->argument('phone'))) {
                if ($password = $this->option('password')) {
                    if ($this->confirm('Изменить пароль?')) {
                        UsersService::changePassword([
                            'id' => $user->id,
                            'password' => $password,
                        ]);

                        $this->info("Пароль пользователя '{$user->full_name}' успешно изменен!");
                    }
                } else {
                    $this->info("Укажите какой параметр изменить для пользователя '{$user->full_name}' (доступно: --password=new_password)");
                }
            } else {
                $this->error('Пользователь не найден');
            }
        } catch(\Exception $e) {
            $this->error("Ошибка");
            $this->info($e->getMessage());

            return;
        }
    }
}
