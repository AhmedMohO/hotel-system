<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ClientsController extends Controller
{
    public function export()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }
}
