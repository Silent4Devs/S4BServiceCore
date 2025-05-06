<?php

namespace Modules\Auth4You\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class Auth4YouController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth4you::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth4you::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('auth4you::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('auth4you::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
