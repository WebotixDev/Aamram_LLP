<?php

namespace App\Repositories\Admin;

use App\Models\DistrictAssociation;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Prettus\Repository\Eloquent\BaseRepository;

class DistrictAssociationRepository extends BaseRepository
{
    function model()
    {
        return DistrictAssociation::class;
    }

    public function index($districtassociationTable)
    {
        if (request()->action) {
            return redirect()->back();
        }
        return view('admin.districtassociation.index', ['tableConfig' => $districtassociationTable]);
    }

    public function create($attribute = [])
    {
        return view('admin.districtassociation.create');
    }
	
	public function edit($id)
    {
        $row = $this->model->find($id);
        return view('admin.districtassociation.edit', [
            'row' => $row,
        ]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            
            $districtassociation = $this->model->create([
                'club_name' => $request->club_name,
                'club_reg_no' => $request->club_reg_no,
                'club_reg_year' => $request->club_reg_year,
                'club_email' => $request->club_email,
                'club_mobile' => $request->club_mobile,
                'club_address' => $request->club_address,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'club_pincode' => $request->club_pincode,
                'club_incharge_name' => $request->club_incharge_name,
                'club_incharge_mobile' => $request->club_incharge_mobile,
                'club_secretory_name' => $request->club_secretory_name,
                'club_secretory_mobile' => $request->club_secretory_mobile,
                'club_secretory_login' => $request->club_secretory_login,
                'reg_no_prefix' => $request->reg_no_prefix,
                'club_bank_name' => $request->club_bank_name,
                'club_branch_name' => $request->club_branch_name,
                'club_bank_acc_name' => $request->club_bank_acc_name,
                'club_bank_acc_no' => $request->club_bank_acc_no,
                'club_bank_ifsc' => $request->club_bank_ifsc,
                'club_pan_no' => $request->club_pan_no,
                'club_aadhar_no' => $request->club_aadhar_no,
                'club_gst_no' => $request->club_gst_no,
                'club_status' => $request->club_status,
                'club_addedby' => Auth::id(),
                'club_updatedby' => Auth::id(),
                'user_id' => Auth::id(),
                
            ]);
           
            if ($request->hasFile('club_logo')) {
                $imagePath = $this->imageStoreUpdate('', $request->club_logo);
                if ($imagePath) {
                    $districtassociation->update(['club_logo' => $imagePath]);
                }
            }
    
            DB::commit();
            return redirect()->route('admin.districtassociation.index')->with('success', __('District Association Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    public function imageStoreUpdate($file_id = '', $file)
    {
        try {
            $file_name = '';
            if (!blank($file)) {
                if (!File::exists(public_path('uploads/district-club'))) {
                    File::makeDirectory(public_path('uploads/district-club'), 0755, true);
                }
                $destinationPath = public_path('uploads/district-club');
                $img = uniqid() . "." . $file->getClientOriginalExtension();
                $file->move($destinationPath, $img);
                $file_name = 'uploads/district-club/' . $img;
                return $file_name;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
    

            public function update(array $attributes, $id)
        {
            DB::beginTransaction();
            try {
                $districtassociation = $this->model->findOrFail($id);
                
                if (isset($attributes['club_logo']) && $attributes['club_logo'] instanceof \Illuminate\Http\UploadedFile) {
                    if ($districtassociation->club_logo && File::exists(public_path($districtassociation->club_logo))) {
                        File::delete(public_path($districtassociation->club_logo));
                    }

                    $imagePath = $this->imageStoreUpdate('', $attributes['club_logo']);
                    if ($imagePath) {
                        $attributes['club_logo'] = $imagePath;
                    }
                }
                $districtassociation->update($attributes);
                DB::commit();
                return redirect()->route('admin.districtassociation.index')->with('success', __('District Association Updated Successfully'));
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
        }

    public function destroy($id)
    {
        
        DB::beginTransaction();
        try {
            $districtassociation = $this->model->findOrFail($id);
            $districtassociation->destroy($id);

            DB::commit();
            return redirect()->back()->with('success', __('District Association Deleted Successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
?>