<?php

namespace App\Repositories\Admin;

use App\Models\TeamGroup;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class TeamGroupRepository extends BaseRepository
{
    function model()
    {
        return TeamGroup::class;
    }

    public function index($teamgroupTable)
    {
        if (request()->action) {
            return redirect()->back();
        }
        return view('admin.teamgroup.index', ['tableConfig' => $teamgroupTable]);
    }

    public function create($attribute = [])
    {
        return view('admin.teamgroup.create');
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        return view('admin.teamgroup.edit', [
            'row' => $row,
        ]);
    }

    public function store($request)
    {
       
        DB::beginTransaction();
        try {
            $teamgroup = $this->model->create([
                'team_group_name' => $request->team_group_name,
                'team_group_min_age'=> $request->team_group_min_age,
                'team_group_max_age' => $request->team_group_max_age,
                'message' => $request->message,
                'team_group_status' => $request->team_group_status,
                'team_group_addedby' => Auth::id(),
                'team_group_updatedby' => Auth::id(),
               
            ]);
            DB::commit();
            return redirect()->route('admin.teamgroup.index')->with('success', __('teamgroup Created Successfully'));
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $teamgroup = $this->model->findOrFail($id);
            $teamgroup->update($request);
    
            DB::commit();
            return redirect()->route('admin.teamgroup.index')->with('success', __('Team Group Updated Successfully'));
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
            $teamgroup = $this->model->findOrFail($id);
            $teamgroup->destroy($id);

            DB::commit();
            return redirect()->back()->with('success', __('Team Group Deleted Successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
?>