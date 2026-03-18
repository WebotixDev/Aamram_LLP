<?php

namespace App\Repositories\Admin;

use App\Models\UmpireLevel;
use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class UmpireLevelRepository extends BaseRepository
{
    function model()
    {
        return UmpireLevel::class;
    }

    public function index($umpirelevelTable)
    {
        if (request()->action) {
            return redirect()->back();
        }
        return view('admin.master.umpirelevel.index', ['tableConfig' => $umpirelevelTable]);
    }

    public function create($attribute = [])
    {
        return view('admin.master.umpirelevel.create');
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        return view('admin.master.umpirelevel.edit', [
            'row' => $row,
        ]);
    }

    public function store($request)
    {
       
        DB::beginTransaction();
        try {
            $umpirelevel = $this->model->create([
                'umpire_level_name'=>$request->umpire_level_name,
                'umpire_level_rate'=>$request->umpire_level_rate,
                'umpire_level_da'=>$request->umpire_level_da,
                'umpire_level_status'=>1,
                'umpire_level_addedby'=>Auth::id(),
                'umpire_level_updatedby'=>Auth::id(),
                'company_id'=>1,
               
            ]);
            DB::commit();
            return redirect()->route('admin.umpirelevel.index')->with('success', __('Umpire Level Created Successfully'));
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $umpirelevel = $this->model->findOrFail($id);
            $umpirelevel->update($request);
    
            DB::commit();
            return redirect()->route('admin.umpirelevel.index')->with('success', __('Umpire level Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            // Optional: Log the exception for debugging
            // Log::error('Update failed for sports season: '.$e->getMessage());
            throw $e;
        }
    }
    

    public function destroy($id)
    {
        
        DB::beginTransaction();
        try {
            $umpirelevel = $this->model->findOrFail($id);
            $umpirelevel->destroy($id);

            DB::commit();
            return redirect()->back()->with('success', __('Umpire Level Deleted Successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
?>