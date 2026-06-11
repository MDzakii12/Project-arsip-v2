<?php

namespace App\Http\Controllers;

use App\Divisi;
use Illuminate\Http\Request;
use Flash;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();
        return view('divisi.index', compact('divisis'));
    }

    public function store(Request $request)
    {
        Divisi::create($request->all());
        Flash::success('Divisi berhasil ditambahkan.');
        return redirect(route('divisi.index'));
    }

    public function destroy($id)
    {
        $divisi = Divisi::findOrFail($id);
        $divisi->delete();
        Flash::success('Divisi berhasil dihapus.');
        return redirect(route('divisi.index'));
    }
}