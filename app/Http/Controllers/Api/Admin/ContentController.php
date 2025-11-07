<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentRequest;
use App\Models\Content;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function index()
    {
        return Content::with('author')->latest()->paginate();
    }

    public function store(ContentRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('contents', 'public');
        }

        $content = Content::create($data);

        return response()->json($content->load('author'), 201);
    }

    public function show(Content $content)
    {
        return $content->load('author');
    }

    public function update(ContentRequest $request, Content $content)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($content->image_path) {
                Storage::disk('public')->delete($content->image_path);
            }
            $data['image_path'] = $request->file('image')->store('contents', 'public');
        }

        $content->update($data);

        return $content->load('author');
    }

    public function destroy(Content $content)
    {
        if ($content->image_path) {
            Storage::disk('public')->delete($content->image_path);
        }
        $content->delete();

        return response()->noContent();
    }
}
