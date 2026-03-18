<?php

namespace App\Http\Controllers\Admin;

use App\Models\Location;
use App\DataTables\LocationDataTable;
use Illuminate\Http\Request;
use App\Repositories\Admin\LocationRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class locationController extends Controller
{
    protected $repository;

    public function __construct(LocationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the subject records.
     *
     * @param LocationDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(LocationDataTable $dataTable)
    {
        return $dataTable->render('admin.Location.index');
    }


    public function create()
    {
        return view('admin.Location.create');
    }


    public function store(Request $request)
    {
        return $this->repository->store($request);
    }


    public function show(Location $Location)
    {
        return view('admin.Location.show', ['Location' => $Location]);
    }

    public function edit(Location $Location)
    {
        $invoice = DB::table('Location')->where('id', $Location->id)->first();
        return view('admin.Location.edit', ['Location' => $Location], compact('invoice'));
    }

    public function update(Request $request, $Location)
    {
        return $this->repository->update($request->all(), $Location);
    }


    public function destroy(Location $Location)
    {
        return $this->repository->destroy($Location->id);
    }
}
