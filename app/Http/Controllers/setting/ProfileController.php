<?php

namespace App\Http\Controllers\setting;

use App\Models\Account;
use App\DataTables\ProfileDataTable;
use Illuminate\Http\Request;
use App\Repositories\ProfileRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ProfileController extends Controller
{
    protected $repository;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the profile records.
     *
     * @param ProfileDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(ProfileDataTable $dataTable)
    {
        return $dataTable->render('admin.profile.index');
    }

    /**
     * Show the form for creating a new profile record.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.profile.create');
    }

    /**
     * Store a newly created profile record.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified profile record.
     *
     * @param Account $account
     * @return \Illuminate\View\View
     */
    public function show(Account $account)
    {
        return view('admin.profile.show');
    }

    /**
     * Show the form for editing the specified profile record.
     *
     * @param Account $account
     * @return \Illuminate\View\View
     */
    public function edit(Account $profile )
    {
        return view('admin.profile.edit', ['accounts' => $profile  ]);
    }
    
    

    /**
     * Update the specified profile record.
     *
     * @param Request $request
     * @param Account $account
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $profile)
    {

        return $this->repository->update($request->all(), $profile);
    }

    /**
     * Remove the specified profile record from storage.
     *
     * @param Account $account
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Account $profile)
    {
        return $this->repository->destroy($profile->id);
    }
}
