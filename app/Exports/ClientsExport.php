<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        // select all column except password and avatar

        return Client::query()->select(['name', 'email', 'mobile',  'country' , 'gender', 'approved_by' , 'approved_at' , 'last_login_at' , 'created_at' , 'updated_at']);
    }
    
    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Country', 'Gender', 'Approved By', 'Approved At', 'Last Login At', 'Created At', 'Updated At'];
    }

    /**
     * @param Client $client
     */
    public function map($client): array
    {
        return [
            $client->name,
            $client->email,
            $client->mobile,
            $client->gender,
            $client->country,
            $client->approved_by,
            $client->approved_at,
            $client->last_login_at,
            $client->created_at,
            $client->updated_at,
        ];
    }
}
