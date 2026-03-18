<?php

namespace App\Repositories\Admin;
use Exception;
use App\Models\SportsSeason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class SportsSeasonRepository extends BaseRepository
{
    function model()
    {
        return SportsSeason::class;
    }

    public function index($sportsseasonTable)
    {
        if (request()->action) {
            return redirect()->back();
        }
        return view('admin.sportsseason.index', ['tableConfig' => $sportsseasonTable]);
    }

    public function create($attribute = [])
    {
        return view('admin.sportsseason.create');
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        return view('admin.sportsseason.edit', [
            'row' => $row,
        ]);
    }

    public function store($request)
    {
       
        DB::beginTransaction();
        try {
            $sportsseason = $this->model->create([
                'sports_season_name' => $request->sports_season_name,
                'sports_season_from'=> $request->sports_season_from,
                'sports_season_to' => $request->sports_season_to,
                'sports_season_status' => $request->sports_season_status,
                'sports_season_addedby' => Auth::id(),
                'sports_season_updatedby' => Auth::id(),
               
            ]);
            DB::commit();
            return redirect()->route('admin.sportsseason.index')->with('success', __('sportsseason Created Successfully'));
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $sportsseason = $this->model->findOrFail($id);
            $sportsseason->update($request);
    
            DB::commit();
            return redirect()->route('admin.sportsseason.index')->with('success', __('Sports Season Updated Successfully'));
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
            $sportseason = $this->model->findOrFail($id);
            $sportseason->destroy($id);

            DB::commit();
            return redirect()->back()->with('success', __('Sport Season Deleted Successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
?>