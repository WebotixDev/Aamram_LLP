<?php

namespace App\Repositories\Admin;

use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class TeamRepository extends BaseRepository
{
    function model()
    {
        return Team::class;
    }

    public function index($teamTable)
    {
        if (request()->action) {
            return redirect()->back();
        }
        return view('admin.team.index', ['tableConfig' => $teamTable]);
    }

    public function create($attribute = [])
    {
        return view('admin.team.create');
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        return view('admin.team.edit', [
            'row' => $row,
        ]);
    }

    public function store($request)
    {
       
        DB::beginTransaction();
        try {
            $team = $this->model->create([
                'club_id' => $request->club_id,
                'team_group_id' => $request->team_group_id,
                'team_name' => $request->team_name,
                'team_short_name' => $request->team_short_name,
                'is_zonal_team' => $request->is_zonal_team,
                'men_women' => $request->men_women,
                'team_status' => $request->team_status ?? 1,
                'team_descr' => '',
                'team_addedby' => Auth::id(),
                'team_updatedby' => Auth::id(),
               
            ]);
            DB::commit();
            return redirect()->route('admin.team.index')->with('success', __('team Created Successfully'));
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $team = $this->model->findOrFail($id);
            $team->update($request);
    
            DB::commit();
            return redirect()->route('admin.team.index')->with('success', __('Team Updated Successfully'));
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