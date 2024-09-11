<?php

namespace App\Http\Controllers;

use App\Models\StorageItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

// use App\Enums\StorageItemTypeEnum;
// use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $fakeItem = function (array $data = []) {
            $name = $data['name'] ?? null;
            $label = $data['label'] ?? $name ?? null;
            $name = is_string($name) ? trim($name) : ucfirst(fake()->word());
            $label = is_string($label) ? trim($label) : $name;

            $typeName = match ($data['typeName'] ?? 'FILE') {
                'FILE' => 'FILE',
                'DIR' => 'DIR',
                default => 'FILE',
            };

            $isDir = $typeName === 'dir';
            $username = 'user01';
            $id = str()->uuid()->toString();

            return [
                'hashid' => hashids_encode($id),
                'base_path' => '/', //"/{$username}",
                'owner_username' => $username,
                'typeName' => $typeName,
                'color' => $isDir ? Arr::random(['red', 'blue', 'green', null]) : null, // dir only
                'name' => $name,
                'label' => $label,
                'favorite' => boolval($data['favorite'] ?? false),
                'updated_at' => now()->subDays(4),
                'updated_at_humans_label' => now()->subDays(4)->diffForHumans(),
                'mime_type' => $isDir ? null : 'application/pdf', // file only
                'size' => $isDir ? null : 1458, // file only
                'size_humans_label' => $isDir ? null : '1.45MB', // file only
            ];
        };

        $items = collect([
            $fakeItem(['typeName' => 'DIR', 'name' => 'School', 'favorite' => true]),
            $fakeItem(['typeName' => 'FILE', 'name' => 'file02.pdf']),
            $fakeItem(['typeName' => 'FILE', 'name' => 'file05.pdf']),
            $fakeItem(['typeName' => 'FILE', 'name' => 'file01.pdf', 'favorite' => true]),
            $fakeItem(['typeName' => 'FILE', 'name' => 'file04.pdf']),
            $fakeItem(['typeName' => 'FILE', 'name' => 'file03.pdf']),
            $fakeItem(['typeName' => 'DIR']),
        ])
            // ->sortBy(fn(array $item) => $item['typeName'] === 'dir' ? -1 : 1) // Good
            ->sortBy('name')
            ->sortBy(fn (array $item) => ($item['size'] ?? 0))
            ->sortBy(fn (array $item) => match ($item['typeName'] ?? null) {
                'DIR' => -1, 'FILE' => 1, default => 2
            }) // Advanced
            ->values();

        return Inertia::render('Files/Index', [
            'items' => $items,
        ]);
    }

    public function tempIndex(Request $request)
    {
        return Inertia::render('Files/TempIndex', [
            'items' => StorageItem::where(
                'user_id',
                $request?->user()?->id
            )->orderBy('type_enum', 'asc')
            ->paginate(100),
        ]);
    }

    public function showSigned(Request $request, string $storageItem, ?string $mode = null)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        abort_unless(Str::isUuid($storageItem), 404);

        $storageItem = StorageItem::where('uuid', $storageItem)->firstOrFail();
        $password = $storageItem?->password;

        if ($password) {
            return 'Please, put the file passowrd!'; // TODO: Aqui renderizar formulário para a pessoa digitar a senha
        }

        /**
         * TODO: create enum
         * 10: render
         * 20: download
         * 30: pdf-protected-render
         */
        $mode = filled($mode) ? hashids_decode($mode)[0] ?? null : 10;

        // TODO: aqui retornar o arquivo de acordo com o $mode (download, protected-pdf)

        return match ($mode) {
            10 => 'render',
            20 => 'download',
            30 => 'pdf-protected-render',
            default => 'render',
        };
    }

    public function upload(Request $request)
    {
        return Inertia::render('Files/Upload', [
            // 'items' => $items,
            'base' => StorageItem::getTargetDirFromRequest($request),
        ]);
    }

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'files_to_upload.*' => 'required|mimetypes:application/pdf',
        ]);

        $to = StorageItem::getTargetDirFromRequest($request);

        if (!$request->hasFile('files_to_upload')) {
            return redirect()->route('files.upload')->with('error', __('Invalid upload files'));
        }

        $files = \Arr::wrap($request->file('files_to_upload'));

        if (!$files) {
            return redirect()->route('files.temp_index')->with('error', __('Invalid upload files'));
        }

        $storedFiles = [];

        foreach ($files as $uploadedFile) {
            /**
             * @var \Illuminate\Http\UploadedFile $uploadedFile
             */

            $storedFiles[] = StorageItem::storeFile(
                user: $request?->user(),
                sourceFile: null,
                originalName: null,
                uploadedFile: $uploadedFile,
                base: $to,
            );
        }

        return redirect()->route('files.temp_index')->with('success', __('Files uploaded successfully'));
    }

    public function renderPdfProtected(Request $request, string $hashedid)
    {
        $file = StorageItem::where('hashedid', $hashedid)->where('mime_type', 'application/pdf')->firstOrFail();

        $path = $file?->storage()?->path($file?->path);

        if (!file_exists($path)) {
            abort(404);
        }

        dd($path);

        return response()->file($path);
    }
}
