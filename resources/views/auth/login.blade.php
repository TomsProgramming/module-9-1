<x-layout title="Log in">
    <section class="min-h-screen px-6 py-8 flex flex-col">
        <x-nav />

        <main class="flex-1 flex items-center justify-center py-16">
            <div class="w-full max-w-md">
                <div class="text-center">
                    <p class="text-blue-500 text-xs font-bold uppercase tracking-wide">Welcome back</p>
                    <h1 class="text-4xl mt-3">Log in to your account</h1>
                    <p class="text-sm text-gray-500 mt-3">Continue reading the latest Laravel From Scratch news.</p>
                </div>

                <div class="bg-gray-100 border border-black border-opacity-5 rounded-xl p-8 mt-10">
                    <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase mb-2">Email address</label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
                                placeholder="you@example.com" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="text-xs font-bold uppercase">Password</label>
                                <a href="#" class="text-xs text-blue-500 hover:text-blue-600">Forgot password?</a>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
                                placeholder="Your password">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="remember" class="mr-3">
                            Remember me
                        </label>

                        <button type="submit"
                            class="w-full transition-colors duration-300 bg-blue-500 hover:bg-blue-600 rounded-full text-xs font-semibold text-white uppercase py-3 px-8">
                            Log in
                        </button>
                    </form>
                </div>

                <p class="text-center text-sm mt-8">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-blue-500 hover:text-blue-600">Sign up</a>
                </p>
            </div>
        </main>
    </section>
</x-layout>