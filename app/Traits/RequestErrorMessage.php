<?php

namespace App\Traits;

trait RequestErrorMessage
{
	/**
	 * Error message
	 * @return array
	 */
	public function messages(): array
	{
		return [
			'after_or_equal' => ':attribute tidak boleh kurang dari :date',
			'max_digits' => ':attribute tidak boleh melebihi :max digit',
			'max' => ':attribute tidak boleh melebihi :max karakter',
			'mimes' => ':attribute harus berbentuk .csv/.xls/xlsx',
			'integer' => ':attribute harus berupa bilangan bulat',
			'min' => ':attribute tidak boleh kurang dari :min',
			'boolean' => ':attribute harus bernilai boolean',
			'numeric' => ':attribute harus berupa bilangan',
			'exists' => ':attribute tidak dapat ditemukan',
			'date' => ':attribute harus berformat tanggal',
			'string' => ':attribute harus berupa teks',
			'email.email' => ':attribute tidak valid',
			'unique' => ':attribute sudah digunakan',
			'file' => ':attribute harus berupa file',
			'required' => ':attribute wajib diisi',
			'array' => ':attribute tidak sesuai',
			'email' => ':attribute tidak valid',
			'in' => ':attribute tidak sesuai',
		];
	}
}
