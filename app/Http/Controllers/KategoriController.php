<?php

namespace App\Http\Controllers;

use App\Models\Kategori; 
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
    // $rsetKategori = Kategori::getKategoriAll()->paginate(10);
    // return view('kategori.index',compact('rsetKategori'))->with('i', (request()->input('page', 1) - 1) * 10);
    $keyword = $request->input('keyword');

    // Query untuk mencari kategori berdasarkan keyword
    $query = DB::table('kategori')
        ->select('id', 'deskripsi',DB::raw('ketKategorik(kategori) as ketkategorik'))
        ->orderBy('kategori', 'asc');

    if (!empty($keyword)) {
        $query->where('deskripsi', 'LIKE', "%$keyword%")
              ->orWhereRaw('ketKategorik(kategori) COLLATE utf8mb4_unicode_ci LIKE ?', ["%$keyword%"]);
    }

    $rsetKategori = $query->paginate(10);

    return view('kategori.index', compact('rsetKategori'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $akategori = array('blank'=>'Pilih Kategori',
                            'M'=>'Barang Modal',
                            'A'=>'Alat',
                            'BHP'=>'Bahan Habis Pakai',
                            'BTHP'=>'Bahan Tidak Habis Pakai'
                            );
        return view('kategori.create',compact('akategori'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'deskripsi'   => 'required | unique:kategori',
            'kategori'    => 'required | in:M,A,BHP,BTHP',
        ]);

        // buat kategori baru


        try {
            DB::beginTransaction(); // Start the transaction

            // Insert a new category using Eloquent
            Kategori::create([
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
                'status' => 'pending',
            ]);

            DB::commit(); // Commit the changes

            // Flash success message to the session
            Session::flash('success', 'Kategori berhasil disimpan!');
            } catch (\Exception $e) {

            DB::rollBack(); // Rollback in case of an exception
            report($e); // Report the exception

            // Flash failure message to the session
            Session::flash('gagal', 'Kategori gagal disimpan!');
            }

        // Kategori::create([
        //     'deskripsi'  => $request->deskripsi,
	    //     'kategori'   => $request->kategori,
        // ]);
        
        //redirect ke kategori index
        return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id)
    {
        $rsetKategori = Kategori::find($id);
        return view('kategori.show', compact('rsetKategori'));
    }

    public function edit(string $id)
    {
        $rsetKategori = Kategori::find($id);
        return view('kategori.edit', compact('rsetKategori'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'deskripsi'   => 'required',
            'kategori'    => 'required',
        ]);

        $rsetKategori = Kategori::find($id);
            $rsetKategori->update([
                'deskripsi'  => $request->deskripsi,
                'kategori'   => $request->kategori,
            ]);
        return redirect()->route('kategori.index')->with(['success' => 'Data Kategori Berhasil Diubah!']);
    }

    public function destroy(string $id)
    {
        if (DB::table('barang')->where('kategori_id', $id)->exists()){
            return redirect()->route('kategori.index')->with(['gagal' => 'Data Gagal Dihapus! Data masih digunakan di tabel barang']);            
        } else {
        $rsetKategori = Kategori::find($id);
        $rsetKategori->delete();
        return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }
    }
}