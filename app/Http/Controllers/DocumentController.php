<?php

namespace App\Http\Controllers;

use App\CustomField;
use App\Document;
use App\File;
use App\FileType;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\CreateFilesRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\FileTypeRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\TagRepository;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Laracasts\Flash\Flash;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Crypt;

class DocumentController extends Controller
{
    private $tagRepository;
    private $documentRepository;
    private $customFieldRepository;
    private $fileTypeRepository;
    private $permissionRepository;

    public function __construct(TagRepository $tagRepository,
                                DocumentRepository $documentRepository,
                                CustomFieldRepository $customFieldRepository,
                                FileTypeRepository $fileTypeRepository,
                                PermissionRepository $permissionRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->documentRepository = $documentRepository;
        $this->customFieldRepository = $customFieldRepository;
        $this->fileTypeRepository = $fileTypeRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);
        $documents = $this->documentRepository->searchDocuments(
            $request->get('search'),
            $request->get('tags'),
            $request->get('status')
        );
        $tags = $this->tagRepository->all();
        return view('documents.index', compact('documents', 'tags'));
    }

    public function create()
    {
        //$this->authorize('create', Document::class);
        $tags = $this->tagRepository->all();
        $customFields = $this->customFieldRepository->getForModel('documents');
        
        // SUNTIKAN GAIB: Ambil daftar nama pegawai untuk Form Admin
        $pegawais = User::pluck('name', 'id');
        
        return view('documents.create', compact('tags', 'customFields', 'pegawais'));
    }

    public function store(CreateDocumentRequest $request)
    {
        $data = $request->all();
        
        // SUNTIKAN GAIB: Jadikan pegawai yang dipilih Admin sebagai Pemilik Arsip
        if (auth()->user()->is_super_admin && $request->has('pemilik_id')) {
            $data['created_by'] = $request->pemilik_id;
        } else {
            $data['created_by'] = Auth::id();
        }

        if (auth()->check() && !auth()->user()->is_super_admin) {
            $data['divisi'] = auth()->user()->divisi;
        }

        $data['status'] = config('constants.STATUS.PENDING');
        // $this->authorize('store', [Document::class, $data['tags']]);

        $document = $this->documentRepository->createWithTags($data);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " Berhasil Disimpan!");
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " Created");

        foreach (config('constants.DOCUMENT_LEVEL_PERMISSIONS') as $perm_key => $perm) {
            Permission::create(['name' => $perm_key . $document->id]);
        }

        if ($request->has('savnup')) {
            return redirect()->route('documents.files.create', $document->id);
        }
        return redirect()->route('documents.index');
    }

    public function show($id)
    {
        $document = $this->documentRepository->getOneEagerLoaded($id,['files', 'files.fileType', 'files.createdBy', 'activities', 'activities.createdBy', 'tags']);
        if (empty($document)) {
            abort(404);
        }
        $this->authorize('view', $document);

        $missigDocMsgs = $this->documentRepository->buildMissingDocErrors($document);
        $dataToRet = compact('document', 'missigDocMsgs');

        if (auth()->user()->can('user manage permission')) {
            $users = User::where('id', '!=', 1)->get();
            $thisDocPermissionUsers = $this->permissionRepository->getUsersWiseDocumentLevelPermissionsForDoc($document);
            $tagWisePermList = $this->permissionRepository->getTagWiseUsersPermissionsForDoc($document);
            $globalPermissionUsers = $this->permissionRepository->getGlobalPermissionsForDoc($document);

            $dataToRet = array_merge($dataToRet, compact('users', 'thisDocPermissionUsers', 'tagWisePermList', 'globalPermissionUsers'));
        }
        return view('documents.show', $dataToRet);
    }

    public function storePermission($id, Request $request)
    {
        abort_if(!auth()->user()->can('user manage permission'), 403, 'This action is unauthorized .');
        $input = $request->all();
        $user = User::findOrFail($input['user_id']);
        $doc_permissions = $input['document_permissions'];
        $document = Document::findOrFail($id);
        $this->permissionRepository->setDocumentLevelPermissionForUser($user,$document,$doc_permissions);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " Permission allocated");
        return redirect()->back();
    }

    public function deletePermission($documentId, $userId)
    {
        abort_if(!auth()->user()->can('user manage permission'), 403, 'This action is unauthorized.');
        $user = User::findOrFail($userId);
        $document = Document::findOrFail($documentId);
        $this->permissionRepository->deleteDocumentLevelPermissionForUser($document,$user);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " Permission removed");
        return redirect()->back();
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $this->authorize('edit', $document);
        $tags = Tag::all();
        $customFields = $this->customFieldRepository->getForModel('documents');
        
        // SUNTIKAN GAIB: Ambil daftar nama pegawai
        $pegawais = User::pluck('name', 'id');
        
        return view('documents.edit', compact('tags', 'customFields', 'document', 'pegawais'));
    }

    public function update(UpdateDocumentRequest $request, $id)
    {
        $document = Document::findOrFail($id);
        $data = $request->all();
        
        // SUNTIKAN GAIB: Admin bisa update pemilik arsip ini
        if (auth()->user()->is_super_admin && $request->has('pemilik_id')) {
            $data['created_by'] = $request->pemilik_id;
        }

        $this->authorize('update', [$document, $data['tags']]);
        $this->documentRepository->updateWithTags($data,$document);
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " Updated");
        Flash::success(ucfirst(config('settings.document_label_singular')) . " Berhasil Diupdate!");
        return redirect()->route('documents.index');
    }

    public function updateFileDetail(\Illuminate\Http\Request $request, $id)
    {
        $file = \App\File::findOrFail($id);

        $file->name = $request->name;
        $file->status = $request->status;
        $file->masa_guna = $request->masa_guna;
        $file->lokasi_hard_copy = $request->lokasi_hard_copy;

        $file->save();

        Flash::success('Detail File Berhasil Diupdate!');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $this->authorize('delete', $document);
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " Deleted");
        $this->documentRepository->deleteWithFiles($document,true);
        Flash::success(ucfirst(config('settings.document_label_singular')) . " Berhasil Dihapus!");
        return redirect()->route('documents.index');
    }

    public function verify($id, Request $request)
    {
        $document = Document::findOrFail($id);
        $this->authorize('verify', $document);
        $action = $request->get('action');
        $comment = $request->get('vcomment',"");
        if (!empty($comment)) {
            $comment = " with comment: <i>" . $comment . "</i>";
        }
        $msg = "";
        if ($action == 'approve') {
            $this->documentRepository->approveDoc($document);
            $msg = "Approved";
        } elseif ($action == 'reject') {
            $this->documentRepository->rejectDoc($document);
            $msg = "Rejected";
        } else {
            abort(404);
        }
        $document->newActivity(ucfirst(config('settings.document_label_singular')) . " $msg $comment");

        Flash::success(ucfirst(config('settings.document_label_singular')) . " $msg Successfully");
        return redirect()->back();
    }

    public function showUploadFilesUi($id)
    {
        $document = Document::find($id);
        if(empty($document)){
            Flash::error("Oh No..., try to create some ".config('settings.document_label_singular')." before uploading ".config('settings.file_label_plural'));
            return redirect()->route('documents.index');
        }
        //$this->authorize('update', [$document, $document->tags->pluck('id')]);
        $fileTypes = FileType::pluck('name', 'id');
        $customFields = $this->customFieldRepository->getForModel('files');
        return view('documents.file_upload', compact('document', 'fileTypes', 'customFields'));
    }

    public function storeFiles($id, CreateFilesRequest $request)
    {

        $document = Document::findOrFail($id);
        $this->authorize('update', [$document, $document->tags->pluck('id')]);
        $filesData = $request->all()['files'] ?? [];
        
        $filesData = $this->prepareFilesData($filesData);
        
        $this->documentRepository->saveFilesWithDoc($filesData, $document);
        $document->newActivity(count($filesData) . " New " . ucfirst(config('settings.file_label_plural')) . " Uploaded to " . ucfirst(config('settings.document_label_singular')));
        Flash::success(ucfirst(config('settings.file_label_plural')) . " Uploaded Successfully");
        if (!$request->ajax()) {
            return redirect()->route('documents.show', ['document' => $document->id]);
        } else {
            return ["msg" => "Success"];
        }
    }

    private function prepareFilesData($filesData){
        $imageVariants = explode(',', config('settings.image_files_resize'));
        foreach ($filesData as $i => $fileData) {
            $file = $filesData[$i]['file'];
            $fileName = $file->hashName();

            // 1. Lakukan Resizing Gambar DULU (sebelum file dienkripsi)
            if (isImage($file->getMimeType())) {
                foreach ($imageVariants as $imageVariant) {
                    $resizeSavePath = "app/files/$imageVariant/";
                    if (!file_exists(storage_path($resizeSavePath))) {
                        mkdir(storage_path($resizeSavePath), 0755, true);
                    }
                    $imageIntervention = Image::make($file->getRealPath());
                    $imageIntervention->resize($imageVariant, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(storage_path($resizeSavePath . $fileName));
                }
                $thumbPath = "app/files/thumb/";
                if (!file_exists(storage_path($thumbPath))) {
                    mkdir(storage_path($thumbPath), 0755, true);
                }
                Image::make($file->getRealPath())
                    ->resize(193, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(storage_path($thumbPath . $fileName));
            }

            // 2. PROSES ENKRIPSI FILE ASLI
            $fileContents = file_get_contents($file->getRealPath());
            $encryptedContents = Crypt::encrypt($fileContents);
            Storage::put('files/original/' . $fileName, $encryptedContents);

            // 3. Simpan data ke database
            $filesData[$i]['custom_fields'] = json_encode($filesData[$i]['custom_fields'] ?? []);
            $filesData[$i]['file'] = $fileName;
            $filesData[$i]['created_by'] = Auth::id();
            $filesData[$i]['created_at'] = now();
            $filesData[$i]['updated_at'] = now();
        }
        return $filesData;
    }

    public function deleteFile($id)
    {
        $file = File::findOrFail($id);
        $this->authorize('delete', $file->document);
        $file->document->newActivity($file->name . " Deleted From " . ucfirst(config('settings.document_label_singular')));
        $this->documentRepository->deleteFile($file);
        Flash::success(ucfirst(config('settings.file_label_singular')) . " Deleted Successfully");
        return redirect()->back();
    }
}