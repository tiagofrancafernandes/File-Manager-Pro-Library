<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Arr;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $fakeItem = function (array $data = []) {
            $name = $data['name'] ?? null;
            $label = $data['label'] ?? $name ?? null;
            $name = is_string($name) ? trim($name) : ucfirst(fake()->word());
            $label = is_string($label) ? trim($label) : $name;

            $type = match ($data['type'] ?? 'file') {
                'file' => 'file',
                'dir' => 'dir',
                default => 'file',
            };

            $isDir = $type === 'dir';
            $username = 'user01';
            $id = str()->uuid()->toString();

            return [
                'hashid' => hashids_encode_hex($id),
                'base_path' => '/', //"/{$username}",
                'owner_username' => $username,
                'type' => $type,
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
            $fakeItem(['type' => 'dir', 'name' => 'School', 'favorite' => true]),
            $fakeItem(['type' => 'file', 'name' => 'file02.pdf']),
            $fakeItem(['type' => 'file', 'name' => 'file05.pdf']),
            $fakeItem(['type' => 'file', 'name' => 'file01.pdf', 'favorite' => true]),
            $fakeItem(['type' => 'file', 'name' => 'file04.pdf']),
            $fakeItem(['type' => 'file', 'name' => 'file03.pdf']),
            $fakeItem(['type' => 'dir']),
        ])
            // ->sortBy(fn(array $item) => $item['type'] === 'dir' ? -1 : 1) // Good
            ->sortBy('name')
            ->sortBy(fn (array $item) => ($item['size'] ?? 0))
            ->sortBy(fn (array $item) => match ($item['type']) {
                'dir' => -1, 'file' => 1, default => 2
            }) // Advanced
            ->values();

        return Inertia::render('Files/Index', [
            'items' => $items,
        ]);
    }
}
