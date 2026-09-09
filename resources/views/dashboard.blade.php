<x-layout title="My Dashboard">
    <section class="px-6 py-8">
        <x-nav />

        <main class="max-w-6xl mx-auto mt-12 pb-16">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-500">Writer workspace</p>
                    <h1 class="mt-2 text-4xl font-semibold">My dashboard</h1>
                    <p class="mt-3 text-sm text-gray-500">Create posts, keep drafts organised, and follow their review status.</p>
                </div>

                <a href="#write-post" class="inline-flex items-center justify-center rounded-full bg-blue-500 px-6 py-3 text-xs font-bold uppercase text-white hover:bg-blue-600">
                    Write a post
                </a>
            </div>

            @if (session('status'))
                <p class="mt-8 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="mt-8 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-bold">Please check the highlighted fields.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="border border-gray-200 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Drafts</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $draftCount }}</p>
                    <p class="mt-1 text-sm text-gray-500">Ready to finish</p>
                </div>
                <div class="border border-gray-200 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">In review</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $pendingCount }}</p>
                    <p class="mt-1 text-sm text-gray-500">Waiting for approval</p>
                </div>
                <div class="border border-gray-200 bg-white p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Published</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $publishedCount }}</p>
                    <p class="mt-1 text-sm text-gray-500">Visible on the site</p>
                </div>
            </div>

            <div class="mt-12 grid gap-10 lg:grid-cols-5">
                <section id="write-post" class="lg:col-span-3">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                        <h2 class="text-2xl font-semibold">{{ $editingPost ? 'Continue writing' : 'Start a new post' }}</h2>
                        <span class="text-xs font-bold uppercase text-gray-400">{{ $editingPost?->status === 'rejected' ? 'Revision' : ($editingPost ? 'Draft' : 'Draft') }}</span>
                    </div>

                    <form method="POST" action="{{ $editingPost ? route('posts.update', $editingPost) : route('posts.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                        @csrf
                        @if ($editingPost)
                            @method('PUT')
                        @endif
                        <div>
                            <label for="title" class="block text-sm font-bold">Title</label>
                            <input id="title" name="title" type="text" value="{{ old('title', $editingPost?->title) }}" placeholder="Give your post a clear title" class="mt-2 w-full border border-gray-300 px-4 py-3 text-sm outline-none focus:border-blue-500">
                            @error('title')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="categories" class="block text-sm font-bold">Categories</label>
                                <div id="category-picker" class="mt-2 flex min-h-[3rem] flex-wrap items-center gap-2 border border-gray-300 bg-white px-3 py-2 focus-within:border-blue-500">
                                    <div id="category-chips" class="contents"></div>
                                    <input id="category-input" type="text" list="category-options" autocomplete="off" placeholder="Choose or create a category" class="min-w-[12rem] flex-1 border-0 px-1 py-1 text-sm outline-none">
                                </div>
                                <input id="categories" name="categories" type="hidden" value="{{ old('categories', $editingPost?->categories->pluck('name')->join(', ')) }}">
                                <datalist id="category-options">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}">
                                    @endforeach
                                </datalist>
                                <p class="mt-2 text-xs text-gray-400">Choose a suggestion or type a new category. Press Enter or use a comma to add another one.</p>
                                @error('categories')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="thumbnail" class="block text-sm font-bold">Cover image</label>
                                @if ($editingPost?->thumbnail)
                                    <img src="{{ $editingPost->thumbnail }}" alt="Current cover image" class="mb-2 h-20 w-full object-cover">
                                @endif
                                <input id="thumbnail" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-3 file:text-xs file:font-bold file:uppercase hover:file:bg-gray-200">
                                @if ($editingPost)
                                    <p class="mt-2 text-xs text-gray-400">Leave empty to keep the current cover image.</p>
                                @endif
                                @error('thumbnail')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="excerpt" class="block text-sm font-bold">Short introduction</label>
                            <textarea id="excerpt" name="excerpt" rows="3" maxlength="1000" placeholder="A short summary that readers will see first..." class="mt-2 w-full resize-y border border-gray-300 px-4 py-3 text-sm outline-none focus:border-blue-500">{{ old('excerpt', $editingPost?->excerpt) }}</textarea>
                            @error('excerpt')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <label for="body" class="block text-sm font-bold">Your story</label>
                                <span class="text-xs text-gray-400">Rich text editor</span>
                            </div>
                            <div id="rte-toolbar" class="mt-2 border border-gray-300 bg-gray-50">
                                <span class="ql-formats">
                                    <select class="ql-header" aria-label="Heading level">
                                        <option selected></option>
                                        <option value="1">Heading 1</option>
                                        <option value="2">Heading 2</option>
                                        <option value="3">Heading 3</option>
                                    </select>
                                    <button class="ql-bold" aria-label="Bold"></button>
                                    <button class="ql-italic" aria-label="Italic"></button>
                                    <button class="ql-underline" aria-label="Underline"></button>
                                    <button class="ql-strike" aria-label="Strikethrough"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-list" value="ordered" aria-label="Numbered list"></button>
                                    <button class="ql-list" value="bullet" aria-label="Bullet list"></button>
                                    <button class="ql-blockquote" aria-label="Quote"></button>
                                    <button class="ql-code-block" aria-label="Code block"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-link" aria-label="Add link"></button>
                                    <button class="ql-clean" aria-label="Clear formatting"></button>
                                    <select class="ql-align" aria-label="Text alignment"></select>
                                </span>
                            </div>
                            <input id="body" name="body" type="hidden" value="{{ old('body') }}" required>
                            <div id="body-editor" class="min-h-[22rem] border border-gray-300 bg-white text-base leading-7"></div>
                            @error('body')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-5">
                            <button type="submit" name="status" value="draft" class="rounded-full bg-blue-500 px-6 py-3 text-xs font-bold uppercase text-white hover:bg-blue-600">{{ $editingPost ? 'Save revision' : 'Save draft' }}</button>
                            <button type="submit" name="status" value="pending" class="rounded-full border border-gray-300 px-6 py-3 text-xs font-bold uppercase hover:border-gray-500">{{ $editingPost ? 'Resubmit for review' : 'Send for review' }}</button>
                            <p class="text-xs text-gray-400">Submission will be reviewed before it appears on the site.</p>
                        </div>
                    </form>
                </section>

                <aside class="lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                        <h2 class="text-2xl font-semibold">Your posts</h2>
                        <a href="#" class="text-xs font-bold uppercase text-blue-500 hover:text-blue-600">View all</a>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @forelse ($posts as $post)
                        <article class="py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-semibold">{{ $post->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">Updated {{ $post->updated_at->diffForHumans() }}</p>
                                </div>
                                <span class="whitespace-nowrap px-3 py-1 text-xs font-bold uppercase {{ $post->status === 'approved' ? 'bg-green-100 text-green-700' : ($post->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500') }}">{{ $post->status === 'approved' ? 'Published' : ($post->status === 'pending' ? 'In review' : ucfirst($post->status)) }}</span>
                            </div>
                            @if (in_array($post->status, ['draft', 'rejected'], true))
                                <a href="{{ route('posts.edit', $post) }}" class="mt-3 inline-block text-xs font-bold uppercase text-blue-500 hover:text-blue-600">{{ $post->status === 'draft' ? 'Continue writing' : 'Edit and resubmit' }}</a>
                            @endif
                        </article>
                        @empty
                            <p class="py-5 text-sm text-gray-500">You have not written a post yet.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </main>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script>
        const categoryInput = document.getElementById('category-input');
        const categoryHidden = document.getElementById('categories');
        const categoryChips = document.getElementById('category-chips');
        const selectedCategories = [];

        const syncCategories = () => {
            categoryHidden.value = selectedCategories.join(', ');
        };

        const renderCategories = () => {
            categoryChips.replaceChildren(...selectedCategories.map((category, index) => {
                const chip = document.createElement('span');
                chip.className = 'inline-flex items-center gap-2 bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700';
                chip.textContent = category;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'text-blue-400 hover:text-blue-700';
                removeButton.setAttribute('aria-label', `Remove ${category}`);
                removeButton.textContent = 'x';
                removeButton.addEventListener('click', () => {
                    selectedCategories.splice(index, 1);
                    renderCategories();
                    syncCategories();
                });

                chip.append(removeButton);
                return chip;
            }));
        };

        const addCategories = (value) => {
            value.split(',').map((category) => category.trim()).filter(Boolean).forEach((category) => {
                if (!selectedCategories.some((selected) => selected.toLowerCase() === category.toLowerCase())) {
                    selectedCategories.push(category);
                }
            });
            categoryInput.value = '';
            renderCategories();
            syncCategories();
        };

        categoryHidden.value.split(',').map((category) => category.trim()).filter(Boolean).forEach((category) => {
            if (!selectedCategories.some((selected) => selected.toLowerCase() === category.toLowerCase())) {
                selectedCategories.push(category);
            }
        });
        renderCategories();
        syncCategories();

        categoryInput.addEventListener('change', () => addCategories(categoryInput.value));
        categoryInput.addEventListener('blur', () => addCategories(categoryInput.value));
        categoryInput.addEventListener('keydown', (event) => {
            if (event.key === ',' || event.key === 'Enter') {
                event.preventDefault();
                addCategories(categoryInput.value);
            }

            if (event.key === 'Backspace' && !categoryInput.value && selectedCategories.length) {
                selectedCategories.pop();
                renderCategories();
                syncCategories();
            }
        });

        const bodyInput = document.getElementById('body');
        const form = bodyInput.form;
        const quill = new Quill('#body-editor', {
            theme: 'snow',
            modules: { toolbar: '#rte-toolbar' },
            placeholder: 'Start writing here... Accents, emoji and every other character are supported.',
        });

        const initialBody = @json(old('body', $editingPost?->body ?? ''));
        if (initialBody) {
            quill.clipboard.dangerouslyPasteHTML(initialBody);
        }

        form.addEventListener('submit', () => {
            addCategories(categoryInput.value);
            bodyInput.value = quill.root.innerHTML;
        });
    </script>
</x-layout>