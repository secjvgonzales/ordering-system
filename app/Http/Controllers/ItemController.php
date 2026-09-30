<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search')->toString());

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->get();

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        return view('admin.items.create');
    }

    public function store(StoreItemRequest $request)
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        Item::create($data);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Item $item)
    {
        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('admin.items.edit', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $request->safe()->except('image');
        $oldImagePath = $item->image_path;

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        $item->update($data);

        if (isset($data['image_path'])) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Item $item)
    {
        $imagePath = $item->image_path;
        $item->delete();
        $this->deleteImage($imagePath);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Item $item)
    {
        $item->update([
            'status' => $item->status === 'active'
                ? 'inactive'
                : 'active',
        ]);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Item status updated successfully.');
    }

    private function storeImage(UploadedFile $image): string
    {
        $filename = 'item-'.Str::uuid().'.'.$image->extension();
        File::ensureDirectoryExists(public_path('images/items'));
        $image->move(public_path('images/items'), $filename);

        return 'images/items/'.$filename;
    }

    private function deleteImage(?string $imagePath): void
    {
        if (! $imagePath || ! preg_match('/^images\/items\/[A-Za-z0-9._-]+$/', str_replace('\\', '/', $imagePath))) {
            return;
        }

        $fullPath = public_path(str_replace('\\', '/', $imagePath));

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
