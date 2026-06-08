<?php

namespace App\Http\Controllers;

use App\DataTables\TagDataTable;
use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Repositories\TagRepository;
use App\Tag;
use Flash;
use Illuminate\Support\Facades\Auth;
use Response;
use Spatie\Permission\Models\Permission;

class TagController extends AppBaseController
{
    private $tagRepository;

    public function __construct(TagRepository $tagRepo)
    {
        $this->tagRepository = $tagRepo;
    }

    public function index(TagDataTable $tagDataTable)
    {
        return $tagDataTable->render('tags.index');
    }

    public function create()
    {
        return view('tags.create');
    }

    public function store(CreateTagRequest $request)
    {
        $input = $request->all();
        
        // Simpan kategori ke database
        $tag = $this->tagRepository->create($input);

        // PENYAKITNYA DI SINI: Ganti $tag->id jadi $tag->id_kategori
        foreach (config('constants.TAG_LEVEL_PERMISSIONS') as $perm_key => $perm) {
            Permission::create(['name' => $perm_key . $tag->id_kategori]);
        }

        Flash::success('Kategori berhasil ditambahkan.');
        return redirect(route('tags.index'));
    }

    public function show($id)
    {
        $tag = $this->tagRepository->find($id);

        if (empty($tag)) {
            Flash::error('Kategori tidak ditemukan');
            return redirect(route('tags.index'));
        }

        return view('tags.show', compact('tag'));
    }

    public function edit($id)
    {
        $tag = $this->tagRepository->find($id);

        if (empty($tag)) {
            Flash::error('Kategori tidak ditemukan');
            return redirect(route('tags.index'));
        }

        return view('tags.edit')->with('tag', $tag);
    }

    public function update($id, UpdateTagRequest $request)
    {
        $tag = $this->tagRepository->find($id);

        if (empty($tag)) {
            Flash::error('Kategori tidak ditemukan');
            return redirect(route('tags.index'));
        }

        $tag = $this->tagRepository->update($request->all(), $id);

        Flash::success('Kategori berhasil diupdate.');
        return redirect(route('tags.index'));
    }

    public function destroy($id)
    {
        $tag = $this->tagRepository->find($id);

        if (empty($tag)) {
            Flash::error('Kategori tidak ditemukan');
            return redirect(route('tags.index'));
        }
        
        // Langsung hajar hapus data tanpa muter-muter nyari permission
        $tag->delete(); 
        
        Flash::success('Kategori berhasil dihapus.');
        return redirect(route('tags.index'));
    }
}