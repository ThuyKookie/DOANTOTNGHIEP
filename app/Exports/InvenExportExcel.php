<?php

namespace App\Exports;
use App\Models\Product;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
class InvenExportExcel implements FromCollection, WithHeadings
{
    use Exportable;
    private $day1;
    private $day2;
    public function __construct($day1, $day2)
    {
        $this->day1 = $day1;
        $this->day2 = $day2;
    }
    

    public function collection()
    {
        $day1 = $this->day1;
        $day2 = $this->day2;

        $formatProduct = [];
        
        $Product = Product::whereBetween('created_at',[$day1,$day2])->get();
        foreach ($Product as $key => $item) {
            $formatProduct[] = [
                'id'      => $item->id,
                'pro_name'   => $item->pro_name,
                //'pro_slug'    => $item->pro_slug,
               // 'pro_avatar'   => $item->pro_avatar,
              //  'pro_view'   => $item->pro_view,
                'pro_pay' => $item->pro_pay,
               // 'pro_description'  => $item->pro_description,
               // 'pro_content'    => $item->pro_content,
                'pro_number'  => $item->pro_number,
              //  'pro_resistant'  => $item->pro_resistant,
               // 'pro_energy'  => $item->pro_energy,
             //   'pro_link'  => $item->pro_link,
                'created_at'  => $item->created_at,
                'updated_at'  => $item->updated_at
            ];
        }

        return collect($formatProduct);
    }

    public function headings(): array
    {
        return [
            'MÃ',
            'TÊN',
           // "pro_slug",
           // 'pro_avatar',
           // 'pro_view',
            'SỐ LƯỢNG BÁN',
          //  'pro_description',
           // 'pro_content',
            'SỐ LƯỢNG CÒN',
           // 'pro_resistant',
            //'pro_energy',
           // 'pro_link',
            'NGÀY TẠO',
            'NGÀY CẬP NHẬT' 
        ];
    }
}
