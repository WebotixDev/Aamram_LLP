<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transporter;
use App\DataTables\TransporterDataTable;
use Illuminate\Http\Request;
use App\Repositories\Admin\TransporterRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TransporterController extends Controller
{
    protected $repository;

    public function __construct(TransporterRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the subject records.
     *
     * @param TransporterDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(TransporterDataTable $dataTable)
    {
        return $dataTable->render('admin.Transporter.index');
    }


    public function create()
    {
        return view('admin.Transporter.create');
    }


    public function store(Request $request)
    {
        return $this->repository->store($request);
    }


    public function show(Transporter $Transporter)
    {
        return view('admin.Transporter.show', ['supplier' => $Transporter]);
    }

    public function edit(Transporter $Transporter)
    {
        $invoice = DB::table('supplier')->where('id', $Transporter->id)->first();
        return view('admin.Transporter.edit', ['Transporter' => $Transporter], compact('invoice'));
    }

    public function update(Request $request, $supplier)
    {
        return $this->repository->update($request->all(), $supplier);
    }


    public function destroy(Transporter $Transporter)
    {
        return $this->repository->destroy($Transporter->id);
    }
}
