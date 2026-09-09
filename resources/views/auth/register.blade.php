<x-layout title="Register">
    <section class="min-h-screen px-6 py-8 flex flex-col">
        <x-nav />

        <main class="flex-1 flex items-center justify-center py-16">
            <div class="w-full max-w-md">
                <div class="text-center">
                    <p class="text-blue-500 text-xs font-bold uppercase tracking-wide">Join the community</p>
                    <h1 class="text-4xl mt-3">Create your account</h1>
                    <p class="text-sm text-gray-500 mt-3">Sign up to keep up with the latest Laravel From Scratch news.</p>
                </div>

                <div class="bg-gray-100 border border-black border-opacity-5 rounded-xl p-8 mt-10">
                    <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase mb-2">Name</label>
                            <input id="name" name="name" type="text" autocomplete="name" required
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
                                placeholder="Your name" value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

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
                            <label for="password" class="block text-xs font-bold uppercase mb-2">Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
                                placeholder="Choose a password">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold uppercase mb-2">Confirm password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" required
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
                                placeholder="Repeat your password">
                        </div>

                        <button type="submit"
                            class="w-full transition-colors duration-300 bg-blue-500 hover:bg-blue-600 rounded-full text-xs font-semibold text-white uppercase py-3 px-8">
                            Create account
                        </button>
                    </form>
                </div>

                <p class="text-center text-sm mt-8">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-blue-500 hover:text-blue-600">Log in</a>
                </p>
            </div>
        </main>
    </section>
</x-layout>