<?php

namespace App\Http\Controllers\setting;

use App\Models\Product_bulk;
use App\DataTables\Product_bulkDataTable;
use Illuminate\Http\Request;
use App\Repositories\Product_bulkRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Product_bulkController extends Controller
{
    protected $repository;

    public function __construct(Product_bulkRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the product bulk records.
     *
     * @param Product_bulkDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(Product_bulkDataTable $dataTable)
    { 
       
        return $dataTable->render('admin.product_bulk.create');
    }

    /**
     * Show the form for creating a new product bulk record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {  
        return view('admin.product_bulk.create');
    }

    /**
     * Store a newly created product bulk record.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified product bulk record.
     *
     * @param Product_bulk $product_bulk
     * @return \Illuminate\View\View
     */
    public function show(Product_bulk $product_bulk)
    {
        return view('admin.product_bulk.show', ['product_bulk' => $product_bulk]);
    }

    /**
     * Show the form for editing the specified product bulk record.
     *
     * @param Product_bulk $product_bulk
     * @return \Illuminate\View\View
     */
    public function edit(Product_bulk $product_bulk)
    {  
        $product = DB::table('product_bulk')->where('id', $product_bulk->id)->first();
        return view('admin.product_bulk.edit', ['product_bulk' => $product_bulk], compact('product'));
    }

    /**
     * Update the specified product bulk record.
     *
     * @param Request $request
     * @param Product_bulk $product_bulk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $product_bulk)
    {
        return $this->repository->update($request->all(), $product_bulk);
    }

    /**
     * Remove the specified product bulk record from storage.
     *
     * @param Product_bulk $product_bulk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product_bulk $product_bulk)
    {
        return $this->repository->destroy($product_bulk->id);
    }
}
