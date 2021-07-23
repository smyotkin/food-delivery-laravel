<?php

namespace App\Exports;

use App\Models\SystemEvents;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel;

class SystemEventsExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $fileName = 'events';

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

    private $query;

    public function __construct($query = null)
    {
        $this->query = $query;
        $this->fileName = ($this->fileName ?? 'events') . '_' . Carbon::now()->format('dmyhi') . '.csv';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $collection = $this->query ?? SystemEvents::all();

        $model = $collection->makeHidden([
            'created_at',
            'user_id',
            'user',
            'data',
        ]);

        $model->map(function($item) {
            $item->user_full_name = $item->user->full_name ?? 'Система';
            $item->additional_data = trim($item->data);

            return $item;
        });

        return $model;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Slug',
            'Метка',
            'Сообщение',
            'Пользователь',
            'Дополнительные данные',
            'Дата создания',
        ];
    }
}
