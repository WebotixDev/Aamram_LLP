<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use App\DataTables\SubjectDataTable;
use Illuminate\Http\Request;
use App\Repositories\Admin\SubjectRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    protected $repository;

    public function __construct(SubjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the subject records.
     *
     * @param SubjectDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(SubjectDataTable $dataTable)
    {
        return $dataTable->render('admin.subject.index');
    }

    /**
     * Show the form for creating a new subject record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.subject.create');
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
    public function show(Subject $subject)
    {
        return view('admin.subject.show', ['subject' => $subject]);
    }

    /**
     * Show the form for editing the specified subject record.
     *
     * @param Subject $subject
     * @return \Illuminate\View\View
     */
    public function edit(Subject $subject)
    {  
        $invoice = DB::table('subjects')->where('id', $subject->id)->first();
        return view('admin.subject.edit', ['subject' => $subject], compact('invoice'));
    }

    /**
     * Update the specified subject record.
     *
     * @param Request $request
     * @param Subject $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $subject)
    {
        return $this->repository->update($request->all(), $subject);
    }

    /**
     * Remove the specified subject record from storage.
     *
     * @param Subject $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Subject $subject)
    {
        return $this->repository->destroy($subject->id);
    }
}
