<?php

namespace App\Http\Controllers\Admin;

use App\Models\Supplier;
use App\DataTables\SupplierDataTable;
use Illuminate\Http\Request;
use App\Repositories\Admin\SupplierRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    protected $repository;

    public function __construct(SupplierRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the subject records.
     *
     * @param SupplierDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(SupplierDataTable $dataTable)
    {
        return $dataTable->render('admin.supplier.index');
    }


    public function create()
    {
        return view('admin.supplier.create');
    }


    public function store(Request $request)
    {
        return $this->repository->store($request);
    }


    public function show(Supplier $supplier)
    {
        return view('admin.supplier.show', ['supplier' => $supplier]);
    }

    public function edit(Supplier $supplier)
    {
        $invoice = DB::table('supplier')->where('id', $supplier->id)->first();
        return view('admin.supplier.edit', ['supplier' => $supplier], compact('invoice'));
    }

    public function update(Request $request, $supplier)
    {
        return $this->repository->update($request->all(), $supplier);
    }


    public function destroy(Supplier $supplier)
    {
        return $this->repository->destroy($supplier->id);
    }
}
