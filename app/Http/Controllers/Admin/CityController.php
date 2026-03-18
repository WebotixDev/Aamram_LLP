<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use App\DataTables\CityDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CityRepository;
use App\Http\Requests\Admin\CreateCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
//use App\Http\Controllers\Admin\District;

class CityController extends Controller
{
    public $repository;

    public function __construct(CityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @param CityDataTable $dataTable
     * @return Renderable
     */
    public function index(CityDataTable $dataTable)
    {
        return $dataTable->render('admin.city.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin.city.create', [
            'countries' => $this->getCountries(),
            'states' => $this->getStates(),
            'districts' => $this->getDistrict(),
        ]);
    }

    /**
     * Fetch all countries.
     * @return Collection
     */
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

    /**
     * Fetch all districts.
     * @return Collection
     */
    public function getDistrict()
    {
        return \App\Models\District::all()->pluck('district_name', 'id');
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
        // Logic to display a specific city's details can be implemented here
    }

    /**
     * Show the form for editing the specified resource.
     * @param City $city
     * @return Renderable
     */
    public function edit(City $city)
    {
        // Get necessary data before returning the view
        $row = City::find($city->id);  // Assuming you're passing a District model instance to this method
        $city = City::all()->pluck('name', 'id');  // Get all district names and their IDs
    
        // Return the correct view with necessary data
        return view('admin.city.edit', [
            'city' => $city,
            'countries' => $this->getCountries(),
            'states' => $this->getStates(),
            'row' => $row,  // Pass the row if needed
            'districts' => $this->getDistrict()  // Pass the district names and IDs if needed
        ]);
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param City $city
     * @return Renderable
     */
    public function update(Request $request, City $city)
    {
        return $this->repository->update($request->all(), isset($city->id) ? $city->id : $request->id);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, City $city)
    {
        return $this->repository->destroy(isset($city->id) ? $city->id : $request->id);
    }
}
