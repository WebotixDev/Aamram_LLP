<?php

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class ProfileRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Account::class;
    }

    /**
     * Store a new account entry for the user in the database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $accountData = [
                'userID'        => Auth::id(),
                'bank_name'     => $request->bank_name,
                'ACNo'          => $request->ACNo,
                'Branch'        => $request->Branch,
                'IFSC'          => $request->IFSC,
                'account_name'      => $request->account_name,
                'accounttype'   => $request->accounttype,
                'default_bank'  => $request->default_bank,
                'created_at'    => now(),
                'updated_at'    => now(),
                'update_id'     => Auth::id(),
            ];

            // Insert account record
            Account::create($accountData);

            DB::commit();
            return redirect()->route('admin.profile.index')->with('success', __('Account Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an account entry for the user.
     */
    public function update(array $request, $id)
    {
        
        DB::beginTransaction();
        try {
            DB::table('accounts')
                ->where('id', $id)
                ->update([
                    'bank_name'     => $request['bank_name'],
                    'ACNo'          => $request['ACNo'],
                    'Branch'        => $request['Branch'],
                    'IFSC'          => $request['IFSC'],
                    'account_name'      => $request['account_name'],
                    'accounttype'   => $request['accounttype'],
                    'default_bank'  => $request['default_bank'],
                    'update_id'     => Auth::id(),
                    'updated_at'    => now(),
                ]);

            DB::commit();
            return redirect()->route('admin.profile.index')->with('success', __('Account Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an account entry.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $account = Account::findOrFail($id);

            $account->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Account Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
