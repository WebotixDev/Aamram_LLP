<?php

namespace App\Repositories\Admin;

use App\Models\ScorerLevel;
use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class ScorerLevelRepository extends BaseRepository
{
    function model()
    {
        return ScorerLevel::class;
    }

    public function index($scorerlevelTable)
    {
        if (request()->action) {
            return redirect()->back();
        }
        return view('admin.scorerlevel.index', ['tableConfig' => $scorerlevelTable]);
    }

    public function create($attribute = [])
    {
        return view('admin.scorerlevel.create');
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        return view('admin.scorerlevel.edit', [
            'row' => $row,
        ]);
    }

    public function store($request)
    {
       
        DB::beginTransaction();
        try {
            $scorerlevel = $this->model->create([
                'scorer_level_name'=>$request->scorer_level_name,
                'scorer_level_rate'=>$request->scorer_level_rate,
                'scorer_level_da'=>$request->scorer_level_da,
                'scorer_level_status'=>1,
                'scorer_level_addedby'=>Auth::id(),
                'scorer_level_updatedby'=>Auth::id(),
                'company_id'=>1,
               
            ]);
            DB::commit();
            return redirect()->route('admin.scorerlevel.index')->with('success', __('Scorer Level Created Successfully'));
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $scorerlevel = $this->model->findOrFail($id);
            $scorerlevel->update($request);
    
            DB::commit();
            return redirect()->route('admin.scorerlevel.index')->with('success', __('Scorer Level Updated Successfully'));
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
            $scorerlevel = $this->model->findOrFail($id);
            $scorerlevel->destroy($id);

            DB::commit();
            return redirect()->back()->with('success', __('Scorer Level Deleted Successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
?>