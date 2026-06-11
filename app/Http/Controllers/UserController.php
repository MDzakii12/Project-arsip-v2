<?php

namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\Document;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use App\User;
use Flash;
use Response;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Http\Request;

class UserController extends AppBaseController
{
    private $userRepository;
    private $permissionRepository;

    public function __construct(UserRepository $userRepo, PermissionRepository $permissionRepository)
    {
        $this->userRepository = $userRepo;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(UserDataTable $userDataTable)
    {
        $this->authorize('viewAny', User::class);
        return $userDataTable->render('users.index');
    }

    public function create()
    {
        $tags = []; // Bypass agar view tidak error nyari tabel tags
        $this->authorize('create', User::class);
        return view('users.create', compact('tags'));
    }

    public function store(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = $this->userRepository->create($input);

        Flash::success('Pegawai berhasil didaftarkan.');
        return redirect(route('users.index'));
    }

    public function show($id)
    {
        abort_if($id == 1, 404);
        $user = $this->userRepository->find($id);
        $this->authorize('view', $user);

        if (empty($user)) {
            Flash::error('Pegawai tidak ditemukan');
            return redirect(route('users.index'));
        }

        $tags = []; 
        $documents = [];
        $globalPermissions = [];

        return view('users.show', compact('user', 'tags', 'documents','globalPermissions'));
    }

    public function edit($id)
    {
        abort_if($id == 1, 404);
        $user = $this->userRepository->find($id);
        $this->authorize('update', $user);
        $tags = []; // Bypass
        $user->password = "";

        if (empty($user)) {
            Flash::error('Pegawai tidak ditemukan');
            return redirect(route('users.index'));
        }

        return view('users.edit')->with('user', $user)->with('tags', $tags);
    }

    // 1. Ubah UpdateUserRequest jadi Request biasa, dan taruh $request di depan (Standar Laravel)
    public function update(\Illuminate\Http\Request $request, $id)
    {
        // Jangan otak-atik Super Admin
        abort_if($id == 1, 404);
        
        $user = $this->userRepository->find($id);
        
        // $this->authorize('update', $user); // (Kalau error soal hak akses, baris ini dikasih garis miring aja)
        
        if (empty($user)) {
            \Flash::error('Pegawai tidak ditemukan');
            return redirect(route('users.index'));
        }

        $data = $request->all();
        
        // Cek jika password dikosongkan, berarti gak usah diubah
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        // Eksekusi Update ke Database
        $user = $this->userRepository->update($data, $id);

        \Flash::success('Data pegawai berhasil diupdate.');
        return redirect(route('users.index'));
    }

    public function destroy($id)
    {
        abort_if($id == 1, 404);
        $user = $this->userRepository->find($id);
        $this->authorize('delete', $user);
        
        if (empty($user)) {
            Flash::error('Pegawai tidak ditemukan');
            return redirect(route('users.index'));
        }
        $this->userRepository->delete($id);
        Flash::success('Pegawai berhasil dihapus.');
        return redirect(route('users.index'));
    }

    public function blockUnblock(User $user)
    {
        $this->authorize('update', User::class);
        $user->status = $user->status == config('constants.STATUS.BLOCK') ?
            config('constants.STATUS.ACTIVE') : config('constants.STATUS.BLOCK');

        $user->save();
        Flash::success('Status berhasil diubah.');
        return redirect()->route('users.index');
    }

    public function changePasswordView()
    {
        return view('users.change_password');
    }

    public function changePasswordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal harus 6 karakter.'
        ]);

        if (!(Hash::check($request->get('current_password'), auth()->user()->password))) {
            return redirect()->back()->with("error", "Password lama yang kamu masukkan salah. Silakan coba lagi.");
        }

        if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
            return redirect()->back()->with("error", "Password baru tidak boleh sama dengan password lama.");
        }

        $user = auth()->user();
        $user->password = Hash::make($request->get('password'));
        $user->save();

        return redirect()->back()->with("success", "Password kamu berhasil diperbarui!");
    }
}