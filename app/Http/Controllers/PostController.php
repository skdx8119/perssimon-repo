<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Nice;
use App\Models\Tag;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $tag = $request->get('tag');

    $query = Post::query();

    if (!empty($tag)) {
        $query->whereHas('tags', function ($query) use ($tag) {
            $query->where('name', 'like', '%' . $tag . '%');
        });
    }

    $posts = $query->orderBy('created_at','desc')->paginate(10);

    $user = auth()->user();

    return view('post.index', compact('posts', 'user'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs=request()->validate([
            'title'=>'required|max:255',
            'body'=>'required|max:1000',
            'image'=>'image|max:1024',
            'tags' => 'nullable|string'
        ]);

        $post=new Post();
        $post->title=$inputs['title'];
        $post->body=$inputs['body'];
        $post->user_id=auth()->user()->id;

        if (request('image')){
            $original = request()->file('image')->getClientOriginalName();
            $name = date('Ymd_His').'_'.$original;
            $path = $request->file('image')->storeAs('images', $name, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $post->image = $path;
        }

        $post->save();

        // タグの保存処理追加
        $tagNames = preg_split('/[\s　]+/u', $request->tags);
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            if(trim($tagName) != '') {  // 追加：タグ名が空白でないことを確認
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                if ($tag) {
                    $tagIds[] = $tag->id;
                }
            }
        }
        $post->tags()->sync($tagIds);

        return redirect()->route('post.create')->with('message', '投稿を作成しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $nice=Nice::where('post_id', $post->id)->where('user_id',auth()->user()->id)->first();
        return view('post.show', compact('post', 'nice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('post.edit', compact('post'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $inputs=$request->validate([
            'title'=>'required|max:255',
            'body'=>'required|max:1000',
            'image'=>'image|max:1024'
        ]);

        $post->title=$inputs['title'];
        $post->body=$inputs['body'];
        if (request('image')){
            $original = request()->file('image')->getClientOriginalName();
            $name = date('Ymd_His').'_'.$original;
            $path = $request->file('image')->storeAs('images', $name, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $post->image = $path;
        }

        // タグの保存処理
        $tagNames = preg_split('/[\s　]+/u', $request->tags);
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            if($tag){
                $tagIds[] = $tag->id;
            }
        }
        $post->tags()->sync($tagIds);

        $post->save();

        return redirect()->route('post.show', $post)->with('message', '投稿を更新しました');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        if($post->image){
            Storage::delete('storage/images/' . $post->image);
        }
        $post->comments()->delete();
        $post->delete();
        return redirect()->route('post.index')->with('message', '投稿を削除しました');
    }

    public function mypost() {
        $user=auth()->user()->id;
        $posts=Post::where('user_id', $user)->orderBy('created_at', 'desc')->get();
        return view('post.mypost', compact('posts'));
    }

    public function mycomment() {
        $user=auth()->user()->id;
        $comments=Comment::where('user_id', $user)->orderBy('created_at', 'desc')->get();
        return view('post.mycomment', compact('comments'));
    }
}
