<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class IKUExport implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct(protected array $data, protected string|null $title)
    {
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return $this->title ?? 'Sheet 1';
    }
}
