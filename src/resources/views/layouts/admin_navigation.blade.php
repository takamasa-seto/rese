<nav x-data="{ open: false }" class="bg-gray-100 border-b border-none">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Settings Dropdown -->
            <div class="flex items-center">
                <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center justify-center p-1 rounded-md bg-blue-600 shadow-md text-white hover:text-gray-500 hover:bg-blue-200 focus:outline-none focus:bg-blue-200 focus:text-gray-500 transition duration-150 ease-in-out">
                            <div>
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path : class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 6h8M4 12h16M4 18h4" />
                                </svg>
                            </div>

                        </button>
                    </x-slot>
                    <x-slot name="content">
                        @if ( Auth::check() )
                            <x-dropdown-link :href="url('/')">
                                {{ __('Go Home') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/my_page')">
                                {{ __('Go to page Mypage') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-dropdown-link onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Logout') }}
                                </x-dropdown-link>
                            </form>
                        @else
                            <x-dropdown-link :href="url('/')">
                                {{ __('Go Home') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.register')">
                                {{ __('Register') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.login')">
                                {{ __('Login') }}
                            </x-dropdown-link>
                        @endif
                    </x-slot>
                </x-dropdown>
                <div>
                    <span class="text-blue-600 ml-2 font-bold text-2xl">
                        Rese
                    </span>
                </div>

            </div>

        </div>
    </div>

</nav>
