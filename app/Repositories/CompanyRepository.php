<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CompanyRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Company::class;
    }

    /**
     * Store a new company in the database.
     */
    public function store(Request $request)
    {
 
        DB::beginTransaction();
        try {
            // Prepare company data
            $companyData = [
                'user_id'        => Auth::id(),
                'name'           => $request->name,
                'client_name'    => $request->client_name,
                'code'           => $request->code,
                'website'        => $request->website,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'mobile'         => $request->mobile,
                'email'          => $request->email,
                'CIN'            => $request->CIN,
                'option'          => $request->option,
                'password'       => bcrypt($request->password),
                'gst_no'         => $request->gst_no,
                'SMTP_HOST'      => $request->SMTP_HOST,
                'port'           => $request->port,
                'user'           => $request->user,
                'pass'           => $request->pass,
                'IMAP_HOST'      => $request->IMAP_HOST,
                'IMAP_PORT'      => $request->IMAP_PORT,
                'trans_cost'     => $request->trans_cost,
                'update_id'      => Auth::id(),
                'adhar'          => $request->adhar,
                'days'           => $request->days,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];


            
            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $destinationPath = public_path('uploads/signatures');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);
                $companyData['sign'] = 'uploads/signatures/' . $fileName;
            }
            
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $destinationPath = public_path('uploads/logos');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);
                $companyData['logo'] = 'uploads/logos/' . $fileName;
            }
            // Insert company record
            Company::create($companyData);

            DB::commit();
            return redirect()->route('admin.company.index')->with('success', __('Company Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an existing company.
     */
    public function update(array $request, $id)
    {

        DB::beginTransaction();
        try {
            $company = Company::findOrFail($id);

            // Prepare updated company data
            $companyData = [
                'name'           => $request['name'],
                'code'           => $request['code'],
                'website'        => $request['website'],
                'address'        => $request['address'],
                'phone'          => $request['phone'],
                'mobile'         => $request['mobile'],
                'email'          => $request['email'],
                'CIN'            => $request['CIN'],
                'option'          => $request['option'],
                'password'       => isset($request['password']) ? bcrypt($request['password']) : $company->password,
                'gst_no'         => $request['gst_no'],
                'trans_cost' => $request['trans_cost'],
                'update_id'      => Auth::id(),
                 'sign'          => isset($request['sign'])? $request['sign'] : $company->sign,
                'logo'          => isset($request['logo'])? $request['logo'] : $company->logo,
                'days'           => $request['days'],
                'updated_at'     => now(),
            ];


            if (request()->hasFile('sign')) {
                $file = request()->file('sign');
                $destinationPath = public_path('uploads/signatures');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Delete the old file if it exists
                if (!empty($companyData['sign']) && file_exists(public_path($companyData['sign']))) {
                    unlink(public_path($companyData['sign']));
                }

                $file->move($destinationPath, $fileName);
                $companyData['sign'] = 'uploads/signatures/' . $fileName;
            }

            if (request()->hasFile('logo')) {
                $file = request()->file('logo');
                $destinationPath = public_path('uploads/logos');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Delete the old file if it exists
                if (!empty($companyData['logo']) && file_exists(public_path($companyData['logo']))) {
                    unlink(public_path($companyData['logo']));
                }

                $file->move($destinationPath, $fileName);
                $companyData['logo'] = 'uploads/logos/' . $fileName;
            }

            // Update the company record
            $company->update($companyData);

            DB::commit();
            return redirect()->route('admin.company.index')->with('success', __('Company Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }





    // public function update(array $request, $id)
    // {
    //     DB::beginTransaction();
        
    //     try {
    //         // Find the company by id
    //         $company = $this->find($id); // Use the repository's `find` method to retrieve the company

    //         // Prepare company data for update
    //         $companyData = [
    //             'name'           => $request['name'],
    //             'code'           => $request['code'],
    //             'website'        => $request['website'],
    //             'address'        => $request['address'],
    //             'phone'          => $request['phone'],
    //             'mobile'         => $request['mobile'],
    //             'email'          => $request['email'],
    //             'CIN'            => $request['CIN'],
    //             'option'         => $request['option'],
    //             'password'       => isset($request['password']) ? bcrypt($request['password']) : $company->password,
    //             'gst_no'         => $request['gst_no'],
    //             'trans_cost'     => $request['trans_cost'],
    //             'update_id'      => Auth::id(),
    //             'sign'           => isset($request['sign']) ? $request['sign'] : $company->sign,
    //             'logo'           => isset($request['logo']) ? $request['logo'] : $company->logo,
    //             'days'           => $request['days'],
    //             'updated_at'     => now(),
    //         ];

    //         // Handle file uploads if a new file is uploaded
    //         if (isset($request['sign']) && $request['sign'] instanceof \Illuminate\Http\UploadedFile) {
    //             if ($company->sign) {
    //                 Storage::disk('public')->delete($company->sign);
    //             }
    //             $signPath = $request['sign']->store('uploads/signatures', 'public');
    //             $companyData['sign'] = $signPath;
    //         }

    //         if (isset($request['logo']) && $request['logo'] instanceof \Illuminate\Http\UploadedFile) {
    //             if ($company->logo) {
    //                 Storage::disk('public')->delete($company->logo);
    //             }
    //             $logoPath = $request['logo']->store('uploads/logos', 'public');
    //             $companyData['logo'] = $logoPath;
    //         }

    //         // Update company record
    //         $company->update($companyData);

    //         DB::commit();
    //         return true; // or return a response based on your use case
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         throw $e;
    //     }
    // }


    /**
     * Delete a company entry.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $company = Company::findOrFail($id);

            // Delete associated files if they exist
            if ($company->sign) {
                Storage::disk('public')->delete($company->sign);
            }

            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            $company->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Company Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
