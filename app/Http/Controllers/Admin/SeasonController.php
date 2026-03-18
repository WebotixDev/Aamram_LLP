<?php

namespace App\Http\Controllers\Admin;

use App\Models\Season;
use App\DataTables\SeasonDataTable;
use Illuminate\Http\Request;
use App\Repositories\Admin\SeasonRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SeasonController extends Controller
{
    protected $repository;

    public function __construct(SeasonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the subject records.
     *
     * @param SeasonDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(SeasonDataTable $dataTable)
    {
        return $dataTable->render('admin.Season.index');
    }

    /**
     * Show the form for creating a new subject record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.Season.create');
    }

    /**
     * Store a newly created Season record.
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
    public function show(Season $subject)
    {
        return view('admin.Season.show', ['subject' => $subject]);
    }

    /**
     * Show the form for editing the specified subject record.
     *
     * @param Subject $subject
     * @return \Illuminate\View\View
     */
    public function edit(Season $Season)
    {
        $invoice = DB::table('subjects')->where('id', $Season->id)->first();
        return view('admin.Season.edit', ['Season' => $Season], compact('invoice'));
    }

    /**
     * Update the specified subject record.
     *
     * @param Request $request
     * @param Subject $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $Season)
    {
        return $this->repository->update($request->all(), $Season);
    }

    /**
     * Remove the specified subject record from storage.
     *
     * @param Subject $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Season $Season)
    {
        return $this->repository->destroy($Season->id);
    }
}
