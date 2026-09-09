<x-layout :title="$post->title">
    <section class="px-6 py-8">
        <x-nav />

        <main class="max-w-6xl mx-auto mt-10 lg:mt-20 space-y-6">
            <article class="max-w-4xl mx-auto lg:grid lg:grid-cols-12 gap-x-10">
                <div class="col-span-4 lg:text-center lg:pt-14 mb-10">
                    <img src="{{ $post->thumbnail ?? '/assets/images/illustration-1.png' }}" alt="{{ $post->title }}"
                        class="rounded-xl">

                    <p class="mt-4 block text-gray-400 text-xs">
                        Published <time>{{ $post->published_at?->diffForHumans() ?? 'soon' }}</time>
                    </p>

                    <div class="flex items-center lg:justify-center text-sm mt-4">
                        <img src="/assets/images/lary-avatar.svg" alt="Lary avatar">
                        <div class="ml-3 text-left">
                            <h5 class="font-bold">{{ $post->user->name }}</h5>
                            <h6>Author</h6>
                        </div>
                    </div>
                </div>

                <div class="col-span-8">
                    <div class="hidden lg:flex justify-between mb-6">
                        <a href="/"
                            class="transition-colors duration-300 relative inline-flex items-center text-lg hover:text-blue-500">
                            <svg width="22" height="22" viewBox="0 0 22 22" class="mr-2">
                                <g fill="none" fill-rule="evenodd">
                                    <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                                    </path>
                                    <path class="fill-current"
                                        d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z">
                                    </path>
                                </g>
                            </svg>

                            Back to Posts
                        </a>

                        <div class="space-x-2">
                            <a href="#"
                                class="px-3 py-1 border border-blue-300 rounded-full text-blue-300 text-xs uppercase font-semibold"
                                style="font-size: 10px">{{ $post->categories->pluck('name')->join(', ') }}</a>
                        </div>
                    </div>

                    <h1 class="font-bold text-3xl lg:text-4xl mb-10">
                        {{ $post->title }}
                    </h1>

                    <div class="space-y-6 lg:text-lg leading-loose">
                        @if (strip_tags($post->body) === $post->body)
                            @foreach (preg_split('/\R\s*\R/u', trim($post->body)) as $paragraph)
                                <p>{!! nl2br(e($paragraph)) !!}</p>
                            @endforeach
                        @else
                            {!! $post->body !!}
                        @endif
                    </div>
                </div>
            </article>

            <section class="max-w-2xl mx-auto">
                <h3 class="font-bold text-2xl mb-6">Comments</h3>

                @auth
                    <div class="bg-gray-100 border border-black border-opacity-5 rounded-xl p-8 mb-8">
                        <h4 class="font-bold text-sm uppercase mb-4">Leave a comment</h4>

                        <form method="POST" action="{{ route('comments.store', $post) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="body" class="sr-only">Comment</label>
                                <textarea id="body" name="body" rows="4" required
                                    placeholder="Share your thoughts..."
                                    class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">{{ old('body') }}</textarea>
                                @error('body')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="transition-colors duration-300 bg-blue-500 hover:bg-blue-600 rounded-full text-xs font-semibold text-white uppercase py-3 px-8">
                                Post comment
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-gray-500 mb-8">
                        <a href="{{ route('login') }}" class="font-semibold text-blue-500 hover:text-blue-600">Log in</a>
                        to leave a comment.
                    </p>
                @endauth

                <div class="space-y-4">
                    @forelse ($post->comments as $comment)
                        <div class="bg-gray-100 border border-black border-opacity-5 rounded-xl p-6">
                            <div class="flex items-center text-sm">
                                <img src="/assets/images/lary-avatar.svg" alt="Commenter avatar">
                                <div class="ml-3">
                                    <h5 class="font-bold">{{ $comment->user->name }}</h5>
                                    <span class="text-gray-400 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <p class="text-sm mt-4 leading-loose">
                                {{ $comment->body }}
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No comments yet. Be the first to share your thoughts!</p>
                    @endforelse
                </div>
            </section>
        </main>

        <footer class="bg-gray-100 border border-black border-opacity-5 rounded-xl text-center py-16 px-10 mt-16">
            <img src="/assets/images/lary-newsletter-icon.svg" alt="" class="mx-auto -mb-6" style="width: 145px;">
            <h5 class="text-3xl">Stay in touch with the latest posts</h5>
            <p class="text-sm">Promise to keep the inbox clean. No bugs.</p>

            <div class="mt-10">
                <div class="relative inline-block mx-auto lg:bg-gray-200 rounded-full">
                    <form method="POST" action="#" class="lg:flex text-sm">
                        <div class="lg:py-3 lg:px-5 flex items-center">
                            <label for="email" class="hidden lg:inline-block">
                                <img src="/assets/images/mailbox-icon.svg" alt="mailbox letter">
                            </label>

                            <input id="email" type="text" placeholder="Your email address"
                                class="lg:bg-transparent pl-4 focus-within:outline-none">
                        </div>

                        <button type="submit"
                            class="transition-colors duration-300 bg-blue-500 hover:bg-blue-600 mt-4 lg:mt-0 lg:ml-3 rounded-full text-xs font-semibold text-white uppercase py-3 px-8">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </footer>
    </section>
</x-layout>
