<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product_size;
use App\DataTables\Product_sizeDataTable;
use Illuminate\Http\Request;
use App\Repositories\Admin\Product_sizeRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Product_sizeController extends Controller
{
    protected $repository;

    public function __construct(Product_sizeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the subject records.
     *
     * @param SubjectDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(Product_sizeDataTable $dataTable)
    {
        return $dataTable->render('admin.product_size.index');
    }

    /**
     * Show the form for creating a new subject record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.product_size.create');
    }

    /**
     * Store a newly created subject record.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified subject record.
     *
     * @param Subject $subject
     * @return \Illuminate\View\View
     */
    public function show(Product_size $product_size)
    {
        return view('admin.product_size.show', ['product_size' => $product_size]);
    }

    /**
     * Show the form for editing the specified subject record.
     *
     * @param Subject $subject
     * @return \Illuminate\View\View
     */
    public function edit(Product_size $product_size)
    {  
        $invoice = DB::table('subjects')->where('id', $product_size->id)->first();
        return view('admin.product_size.edit', ['product_size' => $product_size], compact('invoice'));
    }

    /**
     * Update the specified subject record.
     *
     * @param Request $request
     * @param Subject $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $product_size)
    {
        return $this->repository->update($request->all(), $product_size);
    }

    /**
     * Remove the specified subject record from storage.
     *
     * @param Subject $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product_size $product_size)
    {
        return $this->repository->destroy($product_size->id);
    }
}
