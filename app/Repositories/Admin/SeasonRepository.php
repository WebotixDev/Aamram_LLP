<?php

namespace App\Repositories\Admin;

use App\Models\Season;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;


class SeasonRepository extends BaseRepository
{
    /**
     * Define the model class for the repository.
     */
    public function model()
    {
        return Season::class;
    }

    /**
     * Store a new expense entry for the user in the database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {


            $seasonData = [
                'user_id' =>    Auth::id(),
                'season'   =>$request->season,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            // Insert expense record
            Season::create($seasonData);

            DB::commit();
            return redirect()->route('admin.Season.index')->with('success', __('Season Created Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an expense entry.
     */
    public function update(array $request, $id)
    {
        DB::beginTransaction();
        try {


            DB::table('season')
                ->where('id', $id)
                ->update([
                     'user_id' =>    Auth::id(),
                    'season' =>$request['season'],
                    'updated_at'   => now(),
                ]);

            DB::commit();
            return redirect()->route('admin.Season.index')->with('success', __('Season Updated Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete an expense entry.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $subject = Season::findOrFail($id);

            $subject->delete();

            DB::commit();
            return redirect()->back()->with('success', __('Season Deleted Successfully'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
