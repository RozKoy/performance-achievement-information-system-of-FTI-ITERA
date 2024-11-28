<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheets implements WithMultipleSheets
{
    public function __construct(protected $sheetClass, protected array $names, protected array $data)
    {
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->names as $nameKey => $name) {
            $sheets[] = new $this->sheetClass($this->data[$nameKey], $name);
        }

        return $sheets;
    }
}
