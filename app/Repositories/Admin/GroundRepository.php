<?php

namespace App\Repositories\Admin;

use App\Models\Ground;
use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class GroundRepository extends BaseRepository
{
    function model()
    {
        return Ground::class;
    }

    public function index($groundTable)
    {
        if (request()->action) {
            return redirect()->back();
        }
        return view('admin.ground.index', ['tableConfig' => $groundTable]);
    }

    public function create($attribute = [])
    {
        return view('admin.ground.create');
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        return view('admin.ground.edit', [
            'row' => $row,
        ]);
    }

    public function store($request)
    {
       
        DB::beginTransaction();
        try {
            $ground = $this->model->create([
                'ground_name'=>$request->ground_name,
                'ground_address'=>$request->ground_address,
                'country_id'=>$request->country_id,
                'state_id'=>$request->state_id,
                'city_id'=>$request->city_id,
                'ground_descr'=>$request->ground_descr,
                'ground_mobile'=>$request->ground_mobile,
                'ground_charge'=>$request->ground_charge,
                'ground_status'=>$request->ground_status??1,
                'ground_state_approve'=>'0',
                'ground_addedby'=>Auth::id(),
                'ground_updatedby'=>Auth::id(),
                'user_id'=>Auth::id(),
               
            ]);
            DB::commit();
            return redirect()->route('admin.ground.index')->with('success', __('Ground Created Successfully'));
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $ground = $this->model->findOrFail($id);
            $ground->update($request);
    
            DB::commit();
            return redirect()->route('admin.ground.index')->with('success', __('Ground Updated Successfully'));
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
            $ground = $this->model->findOrFail($id);
            $ground->destroy($id);

            DB::commit();
            return redirect()->back()->with('success', __('Ground Deleted Successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
?>