<?php

namespace App\Http\Controllers\Admin;

use App\Models\District;
use Illuminate\Http\Request;
use App\DataTables\DistrictDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\DistrictRepository;
use App\Http\Requests\Admin\CreateDistrictRequest;
use App\Http\Requests\Admin\UpdateDistrictRequest;

class DistrictController extends Controller
{
    public $repository;

    public function __construct(DistrictRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(DistrictDataTable $dataTable)
    {
        return $dataTable->render('admin.district.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
     
        return view('admin.district.create', [
            'countries' => $this->getCountries(),
            'states' => $this->getStates(),
        ]);
    }

    public function getCountries()
    {
        return \App\Models\Country::all()->pluck('name', 'id');
    }

    /**
     * Fetch all states.
     * @return Collection
     */
    public function getStates()
    {
        return \App\Models\State::all()->pluck('name', 'id');
    }
    public function getDistricts(Request $request)
    {
        if ($request->has('state_id')) {
            $districts = District::where("state_id", $request->state_id)->get(["district_name", "id"]);
            return response()->json(['districts' => $districts]);
        }
        return response()->json(['districts' => []], 400); // Return an empty response if state_id is missing
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        // You can implement logic to show district details
    }

    /**
     * Show the form for editing the specified resource.
     * @param District $district
     * @return Renderable
     */
    public function edit(District $district)
    {
        // Get necessary data before returning the view
        $row = District::find($district->id);  // Assuming you're passing a District model instance to this method
        $districts = District::all()->pluck('name', 'id');  // Get all district names and their IDs
    
        // Return the correct view with necessary data
        return view('admin.district.edit', [
            'district' => $district,
            'countries' => $this->getCountries(),
            'states' => $this->getStates(),
            'row' => $row,  // Pass the row if needed
            'districts' => $districts  // Pass the district names and IDs if needed
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param District $district
     * @return Renderable
     */
    public function update(Request $request, District $district)
    {
        return $this->repository->update($request->all(), isset($district->id) ? $district->id : $request->id);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, District $district)
    {
        return $this->repository->destroy(isset($district->id) ? $district->id : $request->id);
    }
}
