<template>
  <div class="min-h-screen bg-indigo-500 flex flex-col justify-center items-center p-4 sm:p-6 selection:bg-sky-400 selection:text-sky-900">
    <div class="w-full max-w-4xl lg:max-w-5xl bg-indigo-800 rounded-xl shadow-2xl md:flex">
      
      <div class="w-full md:w-1/2 hidden md:block">
        <img src="/images/Background_Login.jpg" alt="Background" class="object-cover w-full h-full rounded-tl-xl rounded-bl-xl" />
      </div>

      <div class="w-full md:w-1/2 p-8 sm:p-10 md:p-12 lg:p-16 flex flex-col justify-center">
        <inertia-link href="/" class="mb-10 mx-auto">
          <logo /> </inertia-link>
        
        <h1 class="text-3xl sm:text-2xl font-medium text-white text-center mb-2 pb-4">
          Đăng Nhập
        </h1>
        

        <form @submit.prevent="login" class="space-y-4">
          <text-input
            v-model="form.email"
            :error="form.errors.email"
            label="Tên đăng nhập"
            type="email"
            autofocus
            autocapitalize="off"
            autocomplete="email"
            
            :required="true"
            labelTextClass="text-white font-semibold"
            inputBgClass="bg-white"
            inputTextClass="text-black placeholder-slate-400"
            inputBorderClass="border-slate-500 focus:border-sky-400 focus:ring-sky-400"
          />
          <text-input
            v-model="form.password"
            :error="form.errors.password"
            label="Mật Khẩu"
            type="password"
            autocomplete="current-password"
            placeholder="Nhập mật khẩu"
            :required="true"
            labelTextClass="text-white font-semibold"
            inputBgClass="bg-white"
            inputTextClass="text-black placeholder-slate-400"
            inputBorderClass="border-slate-500 focus:border-sky-400 focus:ring-sky-400"
          />
          
          <div class="pt-2 w-50 pt-4">
            <loading-button
              :loading="form.processing"
              class="w-full flex justify-center px-6 py-3 bg-indigo-500 hover:bg-white hover:text-black  text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-opacity-75 transition duration-150 ease-in-out"
              type="submit"
            >
              Đăng nhập
            </loading-button>
          </div>
        </form>
        
        </div>
    </div>
    <p class="mt-8 text-center text-sm text-slate-500 text-white">
      Bản quyền &copy; {{ new Date().getFullYear() }} OFFICE CLOK.
    </p>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo' 
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  metaInfo: { title: 'Đăng Nhập Hệ Thống' },
  layout: null, 
  components: {
    LoadingButton,
    Logo, 
    TextInput,
  },
  data() {
    return {
      form: this.$inertia.form({
        email: '', 
        password: '', 
        remember: false,
      }),
    }
  },
  methods: {
    login() {
      this.form.post(this.route('login.store'))
    },
  },
}
</script>