<x-layouts.auth>
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="mb-3">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ trans('ip.Register an account') }}</h1>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf
                <!-- Full Name Input -->
                <div>
                    <x-forms.input label="Full Name" name="name" type="text" placeholder="{{ trans('ip.Full Name') }}" />
                </div>

                <!-- Email Input -->
                <div>
                    <x-forms.input label="Email" name="email" type="email" placeholder="your@email.com" />
                </div>

                <!-- Password Input -->
                <div>
                    <x-forms.input label="Password" name="password" type="password" placeholder="••••••••" />
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <x-forms.input label="Confirm Password" name="password_confirmation" type="password"
                        placeholder="••••••••" />
                </div>

                <!-- Register Button -->
                <x-button type="primary" class="w-full">{{ trans('ip.Create Account') }}</x-button>
            </form>

            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}"
                        class="text-link hover:text-link-hover hover:underline font-medium">{{ trans('ip.Sign in') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>
