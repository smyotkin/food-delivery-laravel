<?php

namespace App\Exports;

use App\Models\User;
use App\Services\PositionsService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UsersExport implements FromCollection, WithHeadings //, WithMapping
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'users';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::CSV;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/csv',
        'charset' => 'windows-1251',
    ];

    public function __construct()
    {
        $this->fileName = ($this->fileName ?? 'users') . '_' . Carbon::now()->format('dmyhi') . '.csv';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $userModel = User::all()->makeHidden([
            'phone',
            'online',
            'full_name',
            'phone_formatted',
            'registered_at',
            'roles',
            'status',
        ]);

        $userModel->map(function($item) {
            $item->role = $item->roles->toArray()[0]['name'] ?? 'Админ';
            $item->status_name = isset($item->roles[0]->status) ? PositionsService::statuses[$item->roles[0]->status]['name'] :
                'Главный админ';

            return $item;
        });

        return $userModel;
    }

    public function headings(): array
    {
        return [
            'ID',
            'ID города',
            'Имя',
            'Фамилия',
            'Создан',
            'Обновлен',
            'Онлайн',
            'Страница',
            'Часовой пояс',
            'Активность',
            'Персонализированные права',
            'Должность',
            'Статус',
        ];
    }
}
