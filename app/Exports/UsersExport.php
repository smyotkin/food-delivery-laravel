<?php

namespace App\Exports;

use App\Models\User;
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
    private $fileName = 'users.csv';

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

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
//        dump(
//            User::orderBy('created_at', 'desc')->first()->toArray()
//        );

//        return User::all()->map(function($item) {
//            dump($item);
//            return $item;
//        });
        $userModel = User::all()->makeHidden([
            'phone',
            'online',
            'full_name',
            'phone_formatted',
            'registered_at',
        ]);
//        $userModel->hidden = ['phone'];

        dump($userModel);

        return $userModel;
    }

    public function headings(): array
    {
        return [
            'id',
            'city_id',
            'first_name',
            'last_name',
            'created_at',
            'updated_at',
//            'phone',
            'last_seen',
            'last_page',
            'timezone',
            'is_active',
            'is_custom_permissions',
//            'full_name',
//            'phone_formatted',
//            'registered_at',
//            'online',
            'status',
            'role',
        ];
    }

//    public function columnFormats(): array
//    {
//        return [
//            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
//        ];
//    }
//
//    public function map($invoice): array
//    {
//        return [
//            $invoice->invoice_number,
//            Date::dateTimeToExcel($invoice->created_at),
//            $invoice->total
//        ];
//    }

//    public function columnFormats(): array
//    {
//        return [
//            'A' => NumberFormat::FORMAT_TEXT,
//            'B' => NumberFormat::FORMAT_NUMBER,
//            'C' => NumberFormat::FORMAT_TEXT,
//            'D' => NumberFormat::FORMAT_TEXT,
//            'E' => NumberFormat::FORMAT_TEXT,
//        ];
//    }
//
//    /**
//     * @return array
//     * @var $softreserve
//     */
//    public function map($softreserve): array
//    {
//        return [
//            $softreserve->item_name,
//            $softreserve->item_id,
//            $softreserve->item_boss,
//            $softreserve->character_name,
//            $softreserve->character->spec->class,
//        ];
//    }
}
