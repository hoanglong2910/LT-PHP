<template>
  <div>
    <portal-target name="dropdown" slim />

    <div class="md:flex md:flex-col bg-gray-50 min-h-screen font-sans text-gray-900">
      <div class="md:h-screen md:flex md:flex-col">

        <div class="md:flex md:flex-shrink-0 z-20 shadow-md relative md:sticky md:top-0">
          
          <div
            class="bg-indigo-900 md:flex-shrink-0 md:w-64 px-6 py-4 flex items-center justify-between md:justify-center transition-all duration-300 border-r border-indigo-800"
          >
            <inertia-link class="flex items-center gap-3 group" href="/">
              <div class="bg-white/10 p-1.5 rounded-lg group-hover:bg-white/20 transition-colors">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                  />
                </svg>
              </div>
              <span class="text-white font-bold text-lg tracking-wider">PASS COMPANY</span>
            </inertia-link>

            <dropdown class="md:hidden" placement="bottom-end" :auto-close="false">
              <svg
                aria-label="Open menu"
                class="text-indigo-200 w-6 h-6 hover:text-white cursor-pointer"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
              </svg>

              <div
                slot="dropdown"
                class="mt-2 px-6 py-4 shadow-xl bg-indigo-800 rounded max-h-[70vh] overflow-y-auto border border-indigo-700"
              >
                <main-menu />
              </div>
            </dropdown>
          </div>

          <div class="bg-indigo-900 w-full p-4 md:py-0 md:px-8 text-sm flex justify-between items-center border-b border-indigo-800">
            
            <div class="flex items-center">
              <span class="text-indigo-300 font-medium mr-2 hidden md:inline">Doanh nghiệp:</span>
              <h2 class="font-bold text-lg text-white uppercase tracking-tight">PASS COMPANY</h2>
            </div>

            <dropdown class="mt-1" placement="bottom-end">
              <div class="flex items-center cursor-pointer select-none group py-2 pl-4" aria-label="User menu">
                
                <div class="text-right mr-3 hidden md:block">
                  <div class="text-sm font-bold text-white group-hover:text-indigo-200 transition-colors">
                    {{ $page.props.auth.user.hovaten }}
                  </div>
                  <div class="text-xs text-indigo-300 font-medium">{{ $page.props.auth.user.email }}</div>
                </div>

                <div
                  class="w-10 h-10 rounded-full bg-indigo-700 border-2 border-indigo-500 shadow-sm
                         flex items-center justify-center text-white font-bold text-lg
                         group-hover:bg-white group-hover:text-indigo-900 group-hover:border-white transition-all"
                >
                  {{ $page.props.auth.user.hovaten ? $page.props.auth.user.hovaten.charAt(0).toUpperCase() : 'U' }}
                </div>

                <icon class="w-4 h-4 text-indigo-300 ml-2 group-hover:text-white transition-colors" name="cheveron-down" />
              </div>

              <div
                slot="dropdown"
                class="mt-3 py-2 shadow-xl bg-white rounded-lg border border-gray-100
                       text-sm w-64 ring-1 ring-black ring-opacity-5 z-50"
              >
                <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50">Cá nhân</div>

                <inertia-link
                  class="flex items-center px-6 py-2.5 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors"
                  :href="route('nhanvien.edit', $page.props.auth.user.id)"
                >
                  <icon name="users" class="w-4 h-4 mr-3 text-gray-400" />
                  Hồ sơ của tôi
                </inertia-link>

                <inertia-link
                  v-if="$page.props.auth.user.nhanvien_id"
                  class="flex items-center px-6 py-2.5 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors"
                  :href="route('chamcong', { nhanvien: $page.props.auth.user.nhanvien_id })"
                >
                  <icon name="office" class="w-4 h-4 mr-3 text-gray-400" />
                  Lịch sử chấm công
                </inertia-link>

                <inertia-link
                  class="flex items-center px-6 py-2.5 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors"
                  :href="route('users.edit', $page.props.auth.user.id)"
                >
                  <icon name="dashboard" class="w-4 h-4 mr-3 text-gray-400" />
                  Đổi mật khẩu
                </inertia-link>

                <div v-if="$page.props.auth.user.role == 2" class="border-t border-gray-100 mt-2 mb-1"></div>
                <div v-if="$page.props.auth.user.role == 2" class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50">
                  Hệ thống
                </div>

                <inertia-link
                  v-if="$page.props.auth.user.role == 2"
                  class="flex items-center px-6 py-2.5 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors"
                  :href="route('users')"
                >
                  <icon name="users" class="w-4 h-4 mr-3 text-gray-400" />
                  Quản lý tài khoản
                </inertia-link>

                <div class="border-t border-gray-100 my-2"></div>

                <inertia-link
                  class="flex items-center px-6 py-3 hover:bg-red-50 text-gray-700 hover:text-red-600 transition-colors font-medium"
                  :href="route('logout')"
                  method="delete"
                  as="button"
                >
                  <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                  </svg>
                  Đăng Xuất
                </inertia-link>
              </div>
            </dropdown>
          </div>
        </div>

        <div class="md:flex md:flex-grow md:overflow-hidden">
          <main-menu
            class="hidden md:block bg-indigo-900 flex-shrink-0 w-64 overflow-y-auto border-r border-indigo-800 overscroll-contain"
          />

          <div class="md:flex-1 px-4 py-8 md:p-10 md:overflow-y-auto md:h-full bg-gray-50" scroll-region>
            <div class="max-w-7xl mx-auto">
              <flash-messages />
              <slot />
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Dropdown from '@/Shared/Dropdown'
import MainMenu from '@/Shared/MainMenu'
import FlashMessages from '@/Shared/FlashMessages'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    MainMenu,
  },
}
</script>