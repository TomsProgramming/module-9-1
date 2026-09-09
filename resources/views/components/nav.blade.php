<nav class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between md:gap-0">
    <div class="flex items-center justify-between">
        <a href="{{ route('home') }}">
            <img src="/assets/images/logo.svg" alt="Laracasts Logo" width="165" height="16">
        </a>
    </div>

    <div class="flex flex-wrap items-center gap-3 md:gap-4">
        <a href="{{ route('home') }}" class="text-xs font-bold uppercase hover:text-blue-500">Home Page</a>

        @auth
            <details class="relative">
                <summary class="flex h-10 cursor-pointer list-none items-center gap-2 rounded-full border border-gray-200 bg-white px-3 text-xs font-bold hover:border-blue-300 hover:text-blue-500 [&::-webkit-details-marker]:hidden">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.12a7.5 7.5 0 0 1 15 0" />
                    </svg>
                    <span class="max-w-24 truncate">{{ auth()->user()->name }}</span>
                    <svg aria-hidden="true" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </summary>

                <div class="absolute right-0 z-10 mt-2 w-48 border border-gray-200 bg-white py-2 shadow-lg">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-xs font-bold uppercase hover:bg-gray-100 hover:text-blue-500">
                        My dashboard
                    </a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.posts.index') }}" class="block px-4 py-3 text-xs font-bold uppercase hover:bg-gray-100 hover:text-blue-500">
                            Admin dashboard
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-3 text-left text-xs font-bold uppercase hover:bg-gray-100 hover:text-blue-500">
                            Log out
                        </button>
                    </form>
                </div>
            </details>
        @else
            <a href="{{ route('login') }}" class="text-xs font-bold uppercase hover:text-blue-500">Log in</a>
            <a href="{{ route('register') }}" class="text-xs font-bold uppercase hover:text-blue-500">Sign up</a>
        @endauth

        <a href="#" class="rounded-full bg-blue-500 px-5 py-3 text-xs font-semibold uppercase text-white hover:bg-blue-600">
            Subscribe for Updates
        </a>
    </div>
</nav>