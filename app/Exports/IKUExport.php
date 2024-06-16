<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class IKUExport implements FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct(protected array $data)
    {
    }

    public function array(): array
    {
        return $this->data;
    }
}
