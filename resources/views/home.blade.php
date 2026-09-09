<x-layout title="Home">
    <section class="px-6 py-8">
        <x-nav />

        <header class="max-w-xl mx-auto mt-20 text-center">
            <h1 class="text-4xl">
                Latest <span class="text-blue-500">Laravel From Scratch</span> News
            </h1>

            <h2 class="inline-flex mt-2">By Lary Laracore <img src="/assets/images/lary-head.svg"
                    alt="Head of Lary the mascot"></h2>

            <p class="text-sm mt-14">
                Another year. Another update. We're refreshing the popular Laravel series with new content.
                I'm going to keep you guys up to speed with what's going on!
            </p>

            <div class="space-y-2 lg:space-y-0 lg:space-x-4 mt-8">
                <!--  Category -->
                <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">
                    <select class="flex-1 appearance-none bg-transparent py-2 pl-3 pr-9 text-sm font-semibold">
                        <option value="category" disabled selected>Category
                        </option>
                        <option value="personal">Personal</option>
                        <option value="business">Business</option>
                    </select>

                    <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
                        height="22" viewBox="0 0 22 22">
                        <g fill="none" fill-rule="evenodd">
                            <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                            </path>
                            <path fill="#222"
                                d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                        </g>
                    </svg>
                </div>

                <!-- Other Filters -->
                <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">
                    <select class="flex-1 appearance-none bg-transparent py-2 pl-3 pr-9 text-sm font-semibold">
                        <option value="category" disabled selected>Other Filters
                        </option>
                        <option value="foo">Foo
                        </option>
                        <option value="bar">Bar
                        </option>
                    </select>

                    <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
                        height="22" viewBox="0 0 22 22">
                        <g fill="none" fill-rule="evenodd">
                            <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                            </path>
                            <path fill="#222"
                                d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                        </g>
                    </svg>
                </div>

                <!-- Search -->
                <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl px-3 py-2">
                    <form method="GET" action="#">
                        <input type="text" name="search" placeholder="Find something"
                            class="bg-transparent placeholder-black font-semibold text-sm">
                    </form>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto mt-6 lg:mt-20 space-y-6">
            @php
                $items = collect($posts->items());
                $hero = $items->first();
                $twoCol = $items->slice(1, 2);
                $rows = $items->slice(3)->chunk(3);
            @endphp

            @if ($items->isEmpty())
                <p class="text-center text-gray-400">No posts yet.</p>
            @else
                <article
                    class="transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl">
                    <div class="py-6 px-5 lg:flex">
                        <div class="flex-1 lg:mr-8">
                            <img src="{{ $hero->thumbnail ?? '/assets/images/illustration-1.png' }}"
                                alt="Blog Post illustration" class="rounded-xl">
                        </div>

                        <div class="flex-1 flex flex-col justify-between">
                            <header class="mt-8 lg:mt-0">
                                <div class="space-x-2">
                                    <a href="#"
                                        class="px-3 py-1 border border-blue-300 rounded-full text-blue-300 text-xs uppercase font-semibold"
                                        style="font-size: 10px">{{ $hero->categories->pluck('name')->join(', ') }}</a>
                                </div>

                                <div class="mt-4">
                                    <h1 class="text-3xl">
                                        {{ $hero->title }}
                                    </h1>

                                    <span class="mt-2 block text-gray-400 text-xs">
                                        Published <time>{{ $hero->published_at?->diffForHumans() ?? 'soon' }}</time>
                                    </span>
                                </div>
                            </header>

                            <div class="text-sm mt-2">
                                <p>
                                    {{ $hero->excerpt }}
                                </p>
                            </div>

                            <footer class="flex justify-between items-center mt-8">
                                <div class="flex items-center text-sm">
                                    <img src="/assets/images/lary-avatar.svg" alt="Lary avatar">
                                    <div class="ml-3">
                                        <h5 class="font-bold">{{ $hero->user->name }}</h5>
                                    </div>
                                </div>

                                <div class="hidden lg:block">
                                    <a href="{{ route('post.show', $hero) }}"
                                        class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8">Read
                                        More</a>
                                </div>
                            </footer>
                        </div>
                    </div>
                </article>

                @if ($twoCol->isNotEmpty())
                    <div class="lg:grid lg:grid-cols-2 lg:gap-6 space-y-6 lg:space-y-0">
                        @foreach ($twoCol as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>
                @endif

                @foreach ($rows as $row)
                    <div class="lg:grid lg:grid-cols-3 lg:gap-6 space-y-6 lg:space-y-0">
                        @foreach ($row as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>
                @endforeach
            @endif

            <div id="more-posts" class="space-y-6"></div>

            @if ($posts->hasMorePages())
                <div class="text-center pt-6" id="load-more-wrapper">
                    <button type="button" id="load-more-btn" data-next-url="{{ $posts->nextPageUrl() }}"
                        class="transition-colors duration-300 inline-block text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-3 px-8">
                        Load More
                    </button>
                </div>
            @endif
        </main>

        @if ($posts->hasMorePages())
            <script>
                document.getElementById('load-more-btn')?.addEventListener('click', async function () {
                    const button = this;
                    const url = button.dataset.nextUrl;

                    button.disabled = true;
                    button.textContent = 'Loading...';

                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    });
                    const data = await response.json();

                    document.getElementById('more-posts').insertAdjacentHTML('beforeend', data.html);

                    if (data.nextPageUrl) {
                        button.dataset.nextUrl = data.nextPageUrl;
                        button.disabled = false;
                        button.textContent = 'Load More';
                    } else {
                        document.getElementById('load-more-wrapper').remove();
                    }
                });
            </script>
        @endif


        <footer class="bg-gray-100 border border-black border-opacity-5 rounded-xl text-center py-16 px-10 mt-16">
            <img src="/assets/images/lary-newsletter-icon.svg" alt="" class="mx-auto -mb-6" style="width: 145px;">
            <h5 class="text-3xl">Stay in touch with the latest posts</h5>
            <p class="text-sm mt-3">Promise to keep the inbox clean. No bugs.</p>

            <div class="mt-10">
                <div class="relative inline-block mx-auto lg:bg-gray-200 rounded-full">

                    <form method="POST" action="#" class="lg:flex text-sm">
                        <div class="lg:py-3 lg:px-5 flex items-center">
                            <label for="email" class="hidden lg:inline-block">
                                <img src="/assets/images/mailbox-icon.svg" alt="mailbox letter">
                            </label>

                            <input id="email" type="text" placeholder="Your email address"
                                class="lg:bg-transparent py-2 lg:py-0 pl-4 focus-within:outline-none">
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
