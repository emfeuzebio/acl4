<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataTableController extends Controller
{
    public function index()
    {
        $data = [
            ['name' => 'John Doe', 'position' => 'Developer', 'age' => 28, 'start_date' => '2018-01-01', 'salary' => '$100,000'],
            ['name' => 'Jane Smith', 'position' => 'Designer', 'age' => 32, 'start_date' => '2016-03-15', 'salary' => '$90,000'],
            ['name' => 'Sam Green', 'position' => 'Manager', 'age' => 40, 'start_date' => '2012-07-30', 'salary' => '$120,000'],
            // Adicione mais dados conforme necessário
        ];

        return view('datatables.index', compact('data'));
    }
}
