<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            投稿の一覧
        </h2>
        <x-validation-errors class="mb-4" :errors="$errors" />

        <x-message :message="session('message')" />

    </x-slot>

    {{-- 投稿一覧表示用のコード --}}

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="mb-4">{{$user->name??''}}さん、こんにちは！</h2>

        <!-- タグ検索フォーム -->
    <div class="mb-4">
        <form method="GET" action="{{ route('post.index') }}">
            <div class="form-group">
                <label for="tag">タグで検索:</label>
                <input type="text" name="tag" id="tag" class="form-control" placeholder="タグ名を入力">
            </div>
            <span class="badgeb">
            <button type="submit" class="btn btn-primary">検索</button>
            </span>
        </form>
    </div>

        @foreach ($posts as $post)
            <div class="mx-4 sm:p-8 mb-8">
                <div class="bg-white w-full rounded-2xl px-10 pt-2 pb-8 shadow-lg hover:shadow-2xl transition duration-500">
                    <div class="mt-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center">
                                <div class="rounded-full w-12 h-12 flex items-center">
                                {{-- アバター表示 --}}
                                <img src="{{ Storage::disk('s3')->url('avatar/'.$user->avatar??'user_default.jpg') }}">
                                </div>
                                <div class="ml-4">
                                    <h1 class="text-lg text-gray-700 font-semibold hover:underline cursor-pointer">
                                        <a href="{{route('post.show', $post)}}">{{ $post->title }}</a>
                                    </h1>
                                    <p class="text-sm text-gray-500"> {{ $post->user->name??'削除されたユーザー' }} • {{$post->created_at->diffForHumans()}}</p>
                                </div>
                            </div>
                            <x-primary-button class="self-end">
                                <a href="{{route('post.show', $post)}}" style="color:white;">コメントする</a>
                            </x-primary-button>
                        </div>
                        <hr class="w-full mt-4">

                        @if($post->tags->count())
                                @foreach($post->tags as $tag)
                                    <a class="badget" href="{{ route('post.index', ['tag' => $tag->name]) }}">#{{ $tag->name }}</a>
                                @endforeach
                        @endif

                        @if($post->image)
                            <img src="{{ Storage::disk('s3')->url($post->image) }}" class="mx-auto flex items-center my-4 rounded-lg" style="height:100px;">
                        @endif
                        <p class="mt-4 text-gray-600 py-4">{{Str::limit ($post->body, 100, ' …' )}} </p>
                        <hr class="w-full mt-4">
                        <div class="flex items-center justify-between mt-2">
                            <div>
                                @if ($post->comments->count())
                                    <span class="badge">
                                        返信 {{$post->comments->count()}}件
                                    </span>
                                @else
                                    <span>コメントはまだありません。</span>
                                @endif

                                <!-- いいねの数を表示 -->
                                @if($post->nices->count())
                                    <span class="badger">
                                        いいね{{$post->nices->count()}}件
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="mb-4">
            {{$posts->links()}}
        </div>
    </div>
</x-app-layout>
