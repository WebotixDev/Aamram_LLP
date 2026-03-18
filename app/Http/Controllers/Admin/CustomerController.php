<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\DataTables\CustomerDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\CustomerRepository;

class CustomerController extends Controller
{
    public $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @param CustomerDataTable $dataTable
     * @return Renderable
     */
    public function index(CustomerDataTable $dataTable)
    {
        return $dataTable->render('admin.customer.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin.customer.create', [
            'countries' => $this->getCountries(),
            'states' => $this->getStates(),
            'districts' => $this->getDistricts(),
           
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
    public function getDistricts()
    {
        return \App\Models\District::all()->pluck('district_name', 'id');
    }

    public function getCitiesByDistrict(Request $request)
    {
        $districtId = $request->district_id;
        $cities = \App\Models\City::where('district_id', $districtId)->pluck('name', 'id');
        
        return response()->json($cities);
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
        $customer = $this->repository->find($id);
        return view('admin.customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param Customer $customer
     * @return Renderable
     */
    public function edit(Customer $customer)
    {
        $Customer = Customer::pluck('id');

        return view('admin.customer.edit', [
            'customer' => $customer,
            'states' => $this->getStates(),
            'districts' => $this->getDistricts(),
        ]);
    }
  

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Customer $customer
     * @return Renderable
     */
    public function update(Request $request, Customer $customer)
    {
        return $this->repository->update($request->all(), $customer->id);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param Customer $customer
     * @return Renderable
     */
    public function destroy(Request $request, Customer $customer)
    {
        return $this->repository->destroy($customer->id);
    }

    /**
     * Fetch districts by state ID.
     * @param Request $request
     * @return JsonResponse
     */
    public function getDistrictsByState(Request $request)
    {
        if ($request->has('state_id')) {
            $districts = \App\Models\District::where('state_id', $request->state_id)->get(['district_name', 'id']);
            return response()->json(['districts' => $districts]);
        }
        return response()->json(['districts' => []], 400);
    }
    
     public function checkMobile($mobile_no)
    {
        // Check if the mobile number exists in the database
        $exists = Customer::where('mobile_no', $mobile_no)->exists();

        // Return a JSON response indicating whether the number exists
        return response()->json(['exists' => $exists]);
    }
}
