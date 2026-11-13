<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    // 1. BEAUTIFY RESPONSE PADA FUNGSI READ ALL
    public function index(Request $request)
    {
        $posts = Post::orderBy("id", "DESC")->paginate(2)->toArray();

        $response = [
            "total_count" => $posts["total"],
            "limit" => $posts["per_page"],
            "pagination" => [
                "next_page" => $posts["next_page_url"],
                "current_page" => $posts["current_page"]
            ],
            "data" => $posts["data"],
        ];

        return response()->json($response, 200);
    }

    // 2. IMPLEMENTASI VALIDASI FUNGSI CREATE
    public function store(Request $request)
    {
        $input = $request->all();

        $validationRules = [
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
            'status'  => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $post = Post::create($input);
        return response()->json($post, 200);
    }

    // 3. READ DETAIL DENGAN ERROR HANDLING 404
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        return response()->json($post, 200);
    }

    // 4. IMPLEMENTASI VALIDASI & ERROR HANDLING FUNGSI UPDATE
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        // Validation
        $validationRules = [
            'title'   => 'required|min:5',
            'content' => 'required|min:10',
            'status'  => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $post->fill($input);
        $post->save();

        return response()->json($post, 200);
    }

    // 5. DELETE DENGAN ERROR HANDLING 404
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        $post->delete();
        return response()->json(['message' => 'deleted successfully', 'post_id' => $id], 200);
    }
}