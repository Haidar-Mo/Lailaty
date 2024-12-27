<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Traits\Responses;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\{
    Auth,
    DB
};
class ReportsController extends Controller
{
    use Responses;

    public function index()
    {

    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request)
    {

       return DB::transaction(function () use ($request){
        $user=Auth::user();
        $user->report()->create($request->validated());
        return $this->sudResponse("! تم ارسال الشكوى بنجاح ");

       });

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
