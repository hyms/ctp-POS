<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Export implements FromCollection, WithHeadings
{
    use Exportable;

    public array $data;
    public array $dataHeading;

    public function collection(): Collection
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->dataHeading;
    }

}
