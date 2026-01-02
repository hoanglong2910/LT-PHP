<template>
  <div class="h-screen w-screen overflow-hidden bg-slate-50 relative">
    <!-- Background decor (soft, corporate) -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -top-24 -left-24 w-[34rem] h-[34rem] bg-indigo-300/35 rounded-full blur-3xl"></div>
      <div class="absolute top-1/3 -right-28 w-[38rem] h-[38rem] bg-purple-300/30 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-28 left-1/3 w-[36rem] h-[36rem] bg-blue-300/25 rounded-full blur-3xl"></div>
      <div class="absolute inset-0 bg-gradient-to-b from-white/80 via-white/45 to-white/80"></div>
    </div>

    <div class="relative h-full flex">
      <!-- LEFT: Login -->
      <div class="flex-1 flex flex-col justify-center px-5 sm:px-10 lg:flex-none lg:w-[520px] xl:w-[560px]">
        <div class="mx-auto w-full max-w-md">
          <!-- Brand -->
          <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-3">
              <!-- Logo placeholder: thay bằng <img src="/images/pass-logo.svg" class="h-9 w-auto" /> khi có logo thật -->
              <div class="relative">
                <div class="absolute -inset-1 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 blur opacity-60"></div>
                <div class="relative rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm p-2.5">
                  <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                    />
                  </svg>
                </div>
              </div>

              <div class="leading-tight">
                <div class="text-[12px] font-semibold text-slate-500 tracking-wider uppercase">
                  PASS Company Portal
                </div>
                <div class="text-xl font-extrabold text-slate-900 tracking-tight">
                  PASS System
                </div>
              </div>
            </div>

            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
              <span class="inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
              <span>Secure Access</span>
            </div>
          </div>

          <!-- Title -->
          <div class="mb-6">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
              Đăng nhập
            </h1>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
              Vui lòng đăng nhập bằng tài khoản công ty. Nếu gặp sự cố, liên hệ Phòng Nhân sự/IT để được hỗ trợ.
            </p>
          </div>

          <!-- Card -->
          <div
            class="rounded-2xl bg-white/85 backdrop-blur
                   shadow-[0_22px_70px_-35px_rgba(15,23,42,0.45)]
                   ring-1 ring-slate-200 p-6 sm:p-7"
          >
            <form @submit.prevent="login" class="space-y-5" novalidate>
              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">
                  Email làm việc
                </label>
                <div class="mt-2">
                  <text-input
                    id="email"
                    v-model="form.email"
                    :error="form.errors.email"
                    type="email"
                    autocomplete="email"
                    placeholder="ten.nhanvien@pass.com"
                    class="block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400
                           focus:outline-none focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-500 sm:text-sm transition"
                  />
                </div>
              </div>

              <!-- Password -->
              <div>
                <div class="flex items-center justify-between">
                  <label for="password" class="block text-sm font-semibold text-slate-700">
                    Mật khẩu
                  </label>

                  <a
                    href="/forgot-password"
                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-500"
                  >
                    Quên mật khẩu?
                  </a>
                </div>

                <div class="mt-2 relative">
                  <text-input
                    id="password"
                    v-model="form.password"
                    :error="form.errors.password"
                    :type="showPassword ? 'text' : 'password'"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full px-4 py-3 pr-16 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400
                           focus:outline-none focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-500 sm:text-sm transition"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-indigo-600 hover:text-indigo-500 select-none"
                    :aria-label="showPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu'"
                  >
                    {{ showPassword ? 'ẨN' : 'HIỆN' }}
                  </button>
                </div>
              </div>

              <!-- Remember -->
              <div class="flex items-center justify-between gap-3">
                <label class="flex items-center select-none cursor-pointer">
                  <input
                    id="remember-me"
                    v-model="form.remember"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded"
                  />
                  <span class="ml-2 text-sm text-slate-800">Ghi nhớ đăng nhập</span>
                </label>

                <span class="text-xs text-slate-500 hidden sm:block">
                  Chỉ bật trên thiết bị cá nhân
                </span>
              </div>

              <!-- Submit -->
              <div class="pt-1">
                <loading-button
                  :loading="form.processing"
                  type="submit"
                  class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl text-sm font-bold text-white shadow-lg
                         bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700
                         hover:from-indigo-700 hover:via-indigo-800 hover:to-purple-800
                         focus:outline-none focus:ring-4 focus:ring-indigo-600/20 transition"
                >
                  <span v-if="!form.processing">Đăng nhập</span>
                  <span v-else>Đang xác thực...</span>
                </loading-button>
              </div>

              <!-- Policies -->
              <div class="pt-2">
                <p class="text-[12px] text-slate-500 leading-relaxed">
                  Bằng việc đăng nhập, bạn đồng ý với
                  <a href="/policy" class="font-semibold text-indigo-600 hover:text-indigo-500">Chính sách sử dụng</a>
                  và
                  <a href="/security" class="font-semibold text-indigo-600 hover:text-indigo-500">Quy định bảo mật</a>.
                </p>
              </div>

              <!-- Error summary -->
              <div v-if="form.errors && form.errors.message" class="rounded-xl bg-rose-50 ring-1 ring-rose-200 p-3">
                <p class="text-sm text-rose-700 font-medium">{{ form.errors.message }}</p>
              </div>
            </form>
          </div>

          <!-- Footer -->
          <div class="mt-8 flex items-center justify-between text-xs text-slate-400">
            <span>© {{ new Date().getFullYear() }} PASS Company</span>
            <span class="hidden sm:inline">Internal System · v1.0</span>
          </div>
        </div>
      </div>

      <!-- RIGHT: Corporate hero (text shadow focus) -->
      <div class="hidden lg:block relative flex-1">
        <img class="absolute inset-0 h-full w-full object-cover" src="/images/Background_Login.jpg" alt="Office" />

        <!-- Overlay nhẹ vừa đủ, không làm tối quá -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/75 via-slate-950/50 to-black/75"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-black/10"></div>

        <!-- optional tiny blur để giảm nhiễu ảnh -->
        <div class="absolute inset-0 backdrop-blur-[1px]"></div>

        <!-- subtle dot grid -->
        <div
          class="absolute inset-0 opacity-20 mix-blend-overlay"
          style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.35) 1px, transparent 0); background-size: 18px 18px;"
        ></div>

        <!-- CONTENT -->
        <div class="absolute inset-0 flex flex-col justify-end p-14 xl:p-20 text-white">
          <div class="max-w-xl">
            <!-- Badge with shadow -->
            <div
              class="inline-flex items-center gap-2 px-4 py-2 rounded-full
                     bg-black/35 ring-1 ring-white/25
                     shadow-[0_10px_25px_rgba(0,0,0,0.65)]
                     mb-6"
            >
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
              <span class="text-sm text-white/95" style="text-shadow: 0 2px 10px rgba(0,0,0,.85);">
                Enterprise HR Management
              </span>
            </div>

            <!-- Title: TEXT SHADOW mạnh -->
            <h2
              class="text-4xl xl:text-5xl font-extrabold leading-tight tracking-tight text-white"
              style="text-shadow: 0 8px 26px rgba(0,0,0,.95), 0 2px 8px rgba(0,0,0,.85);"
            >
              Quản trị nhân sự <span class="text-indigo-200">chuẩn doanh nghiệp</span>
            </h2>

            <!-- Description: TEXT SHADOW vừa -->
            <p
              class="mt-4 text-lg text-white/95 leading-relaxed max-w-md"
              style="text-shadow: 0 4px 14px rgba(0,0,0,.9);"
            >
              Quản lý hồ sơ, chấm công, tính lương và phân quyền truy cập — tập trung, nhanh và minh bạch.
            </p>

            <!-- Feature cards with shadow -->
            <div class="mt-8 grid grid-cols-3 gap-4">
              <div class="rounded-2xl bg-black/30 ring-1 ring-white/25 p-4 shadow-[0_18px_40px_rgba(0,0,0,0.6)]">
                <div class="text-sm font-bold" style="text-shadow: 0 2px 10px rgba(0,0,0,.85);">Hồ sơ</div>
                <div class="text-xs text-white/85 mt-1" style="text-shadow: 0 2px 10px rgba(0,0,0,.8);">Tập trung & dễ tra</div>
              </div>
              <div class="rounded-2xl bg-black/30 ring-1 ring-white/25 p-4 shadow-[0_18px_40px_rgba(0,0,0,0.6)]">
                <div class="text-sm font-bold" style="text-shadow: 0 2px 10px rgba(0,0,0,.85);">Chấm công</div>
                <div class="text-xs text-white/85 mt-1" style="text-shadow: 0 2px 10px rgba(0,0,0,.8);">Theo ca & linh hoạt</div>
              </div>
              <div class="rounded-2xl bg-black/30 ring-1 ring-white/25 p-4 shadow-[0_18px_40px_rgba(0,0,0,0.6)]">
                <div class="text-sm font-bold" style="text-shadow: 0 2px 10px rgba(0,0,0,.85);">Tính lương</div>
                <div class="text-xs text-white/85 mt-1" style="text-shadow: 0 2px 10px rgba(0,0,0,.8);">Tự động & minh bạch</div>
              </div>
            </div>

            <div class="mt-10 text-sm text-white/85" style="text-shadow: 0 3px 12px rgba(0,0,0,.85);">
              <span class="font-semibold text-white">Hỗ trợ:</span> it-support@pass.com • Ext: 123
            </div>
          </div>
        </div>
      </div>
      <!-- /RIGHT -->
    </div>
  </div>
</template>

<script>
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Đăng Nhập | PASS Company' },
  layout: null,
  components: {
    LoadingButton,
    TextInput,
  },
  data() {
    return {
      showPassword: false,
      form: this.$inertia.form({
        email: '',
        password: '',
        remember: false,
      }),
    }
  },
  mounted() {
    // khóa scroll toàn trang
    document.documentElement.classList.add('overflow-hidden')
    document.body.classList.add('overflow-hidden')
  },
  beforeDestroy() {
    // Vue 2
    document.documentElement.classList.remove('overflow-hidden')
    document.body.classList.remove('overflow-hidden')
  },
  methods: {
    login() {
      this.form.post(this.route('login.store'))
    },
  },
}
</script>
