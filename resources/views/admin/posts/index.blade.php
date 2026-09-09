<x-layout title="Admin dashboard">
    <section class="px-6 py-8">
        <x-nav />

        <main class="mx-auto mt-12 max-w-6xl pb-16">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-500">Content moderation</p>
                    <h1 class="mt-2 text-4xl font-semibold">Admin dashboard</h1>
                    <p class="mt-3 text-sm text-gray-500">Review submitted posts before they become visible on the blog.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-xs font-bold uppercase text-blue-500 hover:text-blue-600">Back to my dashboard</a>
            </div>

            @if (session('status'))
                <p class="mt-8 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</p>
            @endif

            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="border border-yellow-200 bg-yellow-50 p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-yellow-700">Waiting for review</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $pendingCount }}</p>
                </div>
                <div class="border border-green-200 bg-green-50 p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-green-700">Approved</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $approvedCount }}</p>
                </div>
                <div class="border border-gray-200 bg-gray-50 p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Rejected</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $rejectedCount }}</p>
                </div>
            </div>

            <section class="mt-12">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <h2 class="text-2xl font-semibold">Posts to review</h2>
                    <span class="text-xs font-bold uppercase text-gray-400">{{ $pendingCount }} pending</span>
                </div>

                @forelse ($pendingPosts as $post)
                    <article class="border-b border-gray-200 py-6">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-3xl">
                                <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase">
                                    <span class="bg-yellow-100 px-3 py-1 text-yellow-700">Pending</span>
                                    <span class="text-gray-400">{{ $post->categories->pluck('name')->join(', ') }}</span>
                                </div>
                                <h3 class="mt-3 text-2xl font-semibold">{{ $post->title }}</h3>
                                <p class="mt-2 text-sm text-gray-500">By {{ $post->user->name }} · Submitted {{ $post->created_at->diffForHumans() }}</p>
                                <p class="mt-4 text-sm leading-6 text-gray-600">{{ $post->excerpt }}</p>
                                <button type="button" data-open-modal="article-modal-{{ $post->id }}" class="mt-3 text-xs font-bold uppercase text-blue-500 hover:text-blue-600">View full article</button>
                            </div>

                            <div class="flex shrink-0 gap-3">
                                <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                                    @csrf
                                    <button type="submit" class="bg-green-600 px-5 py-3 text-xs font-bold uppercase text-white hover:bg-green-700">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.posts.reject', $post) }}">
                                    @csrf
                                    <button type="submit" class="border border-red-300 px-5 py-3 text-xs font-bold uppercase text-red-700 hover:border-red-500">Reject</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="py-10 text-sm text-gray-500">There are no posts waiting for review.</p>
                @endforelse
            </section>

            <section class="mt-16">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <h2 class="text-2xl font-semibold">All blogs</h2>
                    <span class="text-xs font-bold uppercase text-gray-400">{{ $allPosts->count() }} total</span>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse ($allPosts as $post)
                        <article class="py-5">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-semibold">{{ $post->title }}</h3>
                                        <span class="px-3 py-1 text-xs font-bold uppercase {{ $post->status === 'approved' ? 'bg-green-100 text-green-700' : ($post->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500') }}">
                                            {{ $post->status === 'approved' ? 'Published' : ucfirst($post->status) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">By {{ $post->user->name }} · Updated {{ $post->updated_at->diffForHumans() }}</p>
                                    <p class="mt-2 max-w-3xl text-sm text-gray-600">{{ $post->excerpt }}</p>
                                    <button type="button" data-open-modal="article-modal-{{ $post->id }}" class="mt-3 text-xs font-bold uppercase text-blue-500 hover:text-blue-600">View full article</button>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if ($post->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-600 px-4 py-2 text-xs font-bold uppercase text-white hover:bg-green-700">Approve</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.posts.reject', $post) }}">
                                            @csrf
                                            <button type="submit" class="border border-yellow-300 px-4 py-2 text-xs font-bold uppercase text-yellow-700 hover:border-yellow-500">Take offline</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this blog permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="border border-red-300 px-4 py-2 text-xs font-bold uppercase text-red-700 hover:border-red-500">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>

                        <dialog id="article-modal-{{ $post->id }}" class="w-11/12 max-w-4xl border-0 p-0 shadow-2xl">
                            <div class="max-h-[90vh] overflow-y-auto bg-white">
                                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Article preview</p>
                                    <button type="button" data-close-modal class="text-2xl leading-none text-gray-400 hover:text-gray-700" aria-label="Close article preview">&times;</button>
                                </div>
                                @if ($post->thumbnail)
                                    <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="max-h-80 w-full object-cover">
                                @endif
                                <div class="px-6 py-8 sm:px-10">
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase">
                                        <span class="text-blue-500">{{ $post->categories->pluck('name')->join(', ') }}</span>
                                        <span class="text-gray-300">/</span>
                                        <span class="text-gray-400">{{ $post->status === 'approved' ? 'Published' : ucfirst($post->status) }}</span>
                                    </div>
                                    <h2 class="mt-3 text-3xl font-semibold">{{ $post->title }}</h2>
                                    <p class="mt-3 text-sm text-gray-500">By {{ $post->user->name }} · {{ $post->updated_at->diffForHumans() }}</p>
                                    <p class="mt-6 border-l-2 border-blue-400 pl-4 text-base italic leading-7 text-gray-600">{{ $post->excerpt }}</p>
                                    <div class="prose mt-8 max-w-none text-gray-700">
                                        @if (strip_tags($post->body) === $post->body)
                                            @foreach (preg_split('/\R\s*\R/u', trim($post->body)) as $paragraph)
                                                <p>{!! nl2br(e($paragraph)) !!}</p>
                                            @endforeach
                                        @else
                                            {!! $post->body !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </dialog>
                    @empty
                        <p class="py-10 text-sm text-gray-500">There are no blogs yet.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </section>

    <script>
        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById(button.dataset.openModal)?.showModal();
            });
        });

        document.querySelectorAll('dialog').forEach((dialog) => {
            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) {
                    dialog.close();
                }
            });

            dialog.querySelector('[data-close-modal]')?.addEventListener('click', () => dialog.close());
        });
    </script>
</x-layout>
