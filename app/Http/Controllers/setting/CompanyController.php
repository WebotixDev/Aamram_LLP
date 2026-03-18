<?php

namespace App\Http\Controllers\setting;

use App\Models\Company;
use App\DataTables\CompanyDataTable;
use Illuminate\Http\Request;
use App\Repositories\CompanyRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    protected $repository;

    public function __construct(CompanyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the company records.
     *
     * @param CompanyDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(CompanyDataTable $dataTable)
    {
        return $dataTable->render('admin.company.index');
    }

    /**
     * Show the form for creating a new company record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.company.create');
    }

    /**
     * Store a newly created company record.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified company record.
     *
     * @param Company $company
     * @return \Illuminate\View\View
     */
    public function show(Company $company)
    {
        return view('admin.company.show', ['company' => $company]);
    }

    /**
     * Show the form for editing the specified company record.
     *
     * @param Company $company
     * @return \Illuminate\View\View
     */
    public function edit(Company $company)
    {
        return view('admin.company.edit', ['company' => $company]);
    }

    /**
     * Update the specified company record.
     *
     * @param Request $request
     * @param Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $company)
    {
        return $this->repository->update($request->all(), $company);
    }

    /**
     * Remove the specified company record from storage.
     *
     * @param Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Company $company)
    {
        return $this->repository->destroy($company->id);
    }
}
