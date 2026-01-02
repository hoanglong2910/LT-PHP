<template>
  <div>
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
           <inertia-link class="text-indigo-500 hover:text-indigo-700 transition-colors" :href="route('nhanvien')">Nhân Viên</inertia-link>
           <span class="text-gray-300">/</span>
           <span>Thêm Mới</span>
        </h1>
        <p class="text-sm text-gray-500 mt-1">Tạo hồ sơ nhân viên mới vào hệ thống.</p>
      </div>
      <inertia-link class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm" :href="route('nhanvien')">
        Hủy bỏ
      </inertia-link>
    </div>

    <form @submit.prevent="store">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        
        <div class="lg:col-span-1 space-y-6">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="relative inline-block group mb-4">
              <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-50 mx-auto shadow-md bg-gray-50">
                <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-400">
                  <span v-if="form.hovaten" class="text-4xl font-bold text-white">{{ form.hovaten.charAt(0).toUpperCase() }}</span>
                  <icon v-else name="users" class="w-12 h-12" />
                </div>
              </div>
              
              <input class="hidden" type="file" ref="photo" @change="updatePhotoPreview" />
              
              <button @click.prevent="selectNewPhoto" class="mt-4 px-4 py-2 bg-white border border-gray-300 rounded-full text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors uppercase tracking-wide">
                Chọn ảnh thẻ
              </button>
            </div>
            
            <div class="border-t border-gray-100 pt-4 text-left space-y-4">
               <div>
                 <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mã Nhân Viên</label>
                 <text-input v-model="form.manv" :error="form.errors.manv" placeholder="VD: NV001" />
               </div>
               
               <div>
                 <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Họ và Tên</label>
                 <text-input v-model="form.hovaten" :error="form.errors.hovaten" placeholder="Nhập họ tên đầy đủ" class="font-bold text-gray-800" />
               </div>
               
               <div>
                 <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Trạng thái</label>
                 <select-input v-model="form.trangthai" :error="form.errors.trangthai">
                    <option :value="1">Đang làm việc</option>
                    <option :value="0">Đã nghỉ việc</option>
                 </select-input>
               </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-2">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
              <h2 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                 <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                 Tài Khoản Hệ Thống
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <text-input v-model="form.email" :error="form.errors.email" label="Email đăng nhập" placeholder="email@example.com" />
                 
                 <text-input v-model="form.password" :error="form.errors.password" type="password" label="Mật khẩu" placeholder="Nhập mật khẩu..." />

                 <select-input v-model="form.role" :error="form.errors.role" label="Phân quyền hệ thống" class="md:col-span-2">
                    <option :value="0">Nhân viên (Xem cá nhân)</option>
                    <option :value="1">Quản lý (Xem báo cáo)</option>
                    <option :value="2">Quản trị viên (Toàn quyền)</option>
                 </select-input>
              </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
              <h2 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                 <icon name="users" class="w-5 h-5 mr-2 text-indigo-500" /> Thông Tin Cá Nhân
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <text-input v-model="form.sdt" :error="form.errors.sdt" label="Số điện thoại" />
                 <text-input v-model="form.cmnd" :error="form.errors.cmnd" label="CMND/CCCD" type="number" />
                 
                 <select-input v-model="form.gioitinh" :error="form.errors.gioitinh" label="Giới tính">
                   <option :value="null">-- Chọn --</option>
                   <option :value="0">Nam</option>
                   <option :value="1">Nữ</option>
                 </select-input>
                 
                 <text-input v-model="form.ngaysinh" :error="form.errors.ngaysinh" type="date" label="Ngày sinh" />
                 
                 <text-input v-model="form.diachi" :error="form.errors.diachi" label="Địa chỉ hiện tại" class="md:col-span-2" />
                 <text-input v-model="form.quequan" :error="form.errors.quequan" label="Quê quán" class="md:col-span-2" />
                 
                 <select-input v-model="form.dantoc" :error="form.errors.dantoc" label="Dân tộc">
                   <option :value="null">-- Chọn --</option>
                   <option v-for="td in dantoc" :key="td.id" :value="td.id">{{ td.tendt }}</option>
                 </select-input>
                 
                 <select-input v-model="form.tongiao" :error="form.errors.tongiao" label="Tôn giáo">
                   <option :value="null">-- Chọn --</option>
                   <option v-for="tg in tongiao" :key="tg.id" :value="tg.id">{{ tg.tentg }}</option>
                 </select-input>
              </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
              <h2 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
                 <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                 Công Việc & Lương
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <select-input v-model="form.phucap" :error="form.errors.phucap" label="Vị trí (Phòng ban -> Chức vụ)" class="md:col-span-2">
                   <option :value="null">-- Chọn vị trí công việc --</option>
                   <option v-for="pc in phucap" :key="pc.id" :value="pc.id">{{ pc.tenpb }} -> {{ pc.tencv }}</option>
                 </select-input>

                 <text-input v-model="form.hesoluong" :error="form.errors.hesoluong" label="Hệ số lương" placeholder="VD: 2.34" />
                 
                 <select-input v-model="form.bacluong" :error="form.errors.bacluong" label="Bậc lương">
                    <option :value="null">-- Chọn bậc --</option>
                    <option v-for="i in 10" :key="i" :value="i">Bậc {{ i }}</option>
                 </select-input>

                 <select-input v-model="form.bangcap" :error="form.errors.bangcap" label="Bằng cấp">
                   <option :value="null">-- Chọn bằng cấp --</option>
                   <option v-for="bc in bangcap" :key="bc.id" :value="bc.id">{{ bc.tenbc }}</option>
                 </select-input>

                 <select-input v-model="form.chuyenmon" :error="form.errors.chuyenmon" label="Chuyên môn">
                   <option :value="null">-- Chọn chuyên môn --</option>
                   <option v-for="cm in chuyenmon" :key="cm.id" :value="cm.id">{{ cm.tencm }}</option>
                 </select-input>

                 <select-input v-model="form.ngoaingu" :error="form.errors.ngoaingu" label="Ngoại ngữ">
                   <option :value="null">-- Chọn ngoại ngữ --</option>
                   <option v-for="ng in ngoaingu" :key="ng.id" :value="ng.id">{{ ng.tenng }}</option>
                 </select-input>
              </div>
            </div>

            <div class="flex items-center justify-end">
              <loading-button :loading="form.processing" class="btn-indigo px-8 py-3 rounded-lg shadow-lg font-bold" type="submit">
                Tạo Hồ Sơ Mới
              </loading-button>
            </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/StyledTextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import Icon from '@/Shared/Icon'

export default {
  metaInfo: { title: 'Thêm Mới Nhân Viên' },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Icon,
  },
  props: {
    phucap: Array,
    bangcap: Array,
    chuyenmon: Array,
    ngoaingu: Array,
    tongiao: Array,
    dantoc: Array,
  },
  remember: 'form',
  data() {
    return {
      photoPreview: null,
      form: this.$inertia.form({
        manv: '',
        hovaten: '',
        // Bổ sung các trường tài khoản
        email: '',
        password: '',
        role: 1, // Mặc định là Nhân viên (1) hoặc bạn chỉnh thành 0 nếu 0 là nhân viên
        
        phucap: null,
        bangcap: null,
        ngoaingu: null,
        chuyenmon: null,
        tongiao: null,
        dantoc: null,
        trangthai: 1,
        gioitinh: null,
        ngaysinh: '',
        sdt: '',
        cmnd: '',
        diachi: '',
        quequan: '',
        bacluong: null,
        hesoluong: '',
        photo: null,
      }),
    }
  },
  methods: {
    store() {
      if (this.$refs.photo && this.$refs.photo.files[0]) {
        this.form.photo = this.$refs.photo.files[0]
      }
      this.form.post(this.route('nhanvien.store'))
    },
    selectNewPhoto() {
      this.$refs.photo.click()
    },
    updatePhotoPreview() {
      const reader = new FileReader()
      reader.onload = (e) => {
        this.photoPreview = e.target.result
      }
      reader.readAsDataURL(this.$refs.photo.files[0])
    },
  },
}
</script>