<nav class="md:flex md:justify-between md:items-center">
    <div>
        <a href="{{ route('home') }}">
            <img src="/assets/images/logo.svg" alt="Laracasts Logo" width="165" height="16">
        </a>
    </div>

    <div class="mt-8 md:mt-0">
        <a href="{{ route('home') }}" class="text-xs font-bold uppercase hover:text-blue-500">Home Page</a>

        @auth
            <span class="ml-3 text-xs font-bold">Welcome, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="ml-3 text-xs font-bold uppercase hover:text-blue-500">
                    Log out
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="ml-3 text-xs font-bold uppercase hover:text-blue-500">Log in</a>
            <a href="{{ route('register') }}" class="ml-3 text-xs font-bold uppercase hover:text-blue-500">Sign up</a>
        @endauth

        <a href="#" class="bg-blue-500 ml-3 rounded-full text-xs font-semibold text-white uppercase py-3 px-5">
            Subscribe for Updates
        </a>
    </div>
</nav>