<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\InvoicesImport;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index(): JsonResponse
    {
//        return response()->json(["success" => true, 'data' => Invoice::all()], 200);
        $excel = Excel::import(new InvoicesImport, "C:\Users\mechavarria\OneDrive - aldereca.com\Desktop\Book1.xlsx");

        return response()->json(["success" => true, 'data' => $excel], 200);
    }
}
