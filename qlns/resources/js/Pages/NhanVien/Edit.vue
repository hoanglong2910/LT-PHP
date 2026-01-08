<template>
  <div>
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
           <inertia-link class="text-indigo-500 hover:text-indigo-700 transition-colors" :href="route('nhanvien')">Nhân Viên</inertia-link>
           <span class="text-gray-300">/</span>
           <span>{{ form.hovaten }}</span>
        </h1>
      </div>
      
      <div v-if="$page.props.auth.user.role > 0" class="flex flex-wrap gap-2">
         <inertia-link :href="route('thuongphat.create', nhanvien.id)" class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
            Thưởng/Phạt
         </inertia-link>
         <inertia-link :href="route('ungluong.create', nhanvien.id)" class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
            Ứng Lương
         </inertia-link>
         <inertia-link :href="route('nhanluong.create', nhanvien.id)" class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
            Nhận Lương
         </inertia-link>
         <inertia-link :href="route('nghiviec.create', nhanvien.id)" class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-red-600 transition-colors shadow-sm">
            Nghỉ Việc
         </inertia-link>
         <inertia-link :href="route('chamcong', { nhanvien: nhanvien.id })" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-md transition-colors">
            Xem Chấm Công
         </inertia-link>
      </div>
    </div>

    <trashed-message v-if="nhanvien.deleted_at" class="mb-6" @restore="restore">
       Nhân viên này đã bị xoá. Bấm vào đây để khôi phục.
    </trashed-message>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
      
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
          <div class="relative inline-block group mb-4">
            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-indigo-50 mx-auto shadow-md">
              <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
              <img v-else-if="nhanvien.photo" :src="nhanvien.photo" class="w-full h-full object-cover" />
              <div v-else class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-4xl">
                {{ form.hovaten ? form.hovaten.charAt(0).toUpperCase() : 'NV' }}
              </div>
            </div>
            
            <input class="hidden" type="file" ref="photo" accept="image/*" @change="updatePhotoPreview" />
            
            <button v-if="$page.props.auth.user.role > 0" @click.prevent="selectNewPhoto" class="mt-4 px-4 py-2 bg-white border border-gray-300 rounded-full text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors uppercase tracking-wide">
              Đổi ảnh
            </button>
          </div>
          
          <div class="border-t border-gray-100 pt-4 text-left space-y-4">
             <div>
               <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Họ và Tên</label>
               <text-input v-model="form.hovaten" :error="form.errors.hovaten" class="font-bold text-gray-800" :disabled="$page.props.auth.user.role == 0" />
             </div>
             
             <div>
               <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Trạng thái</label>
               <select-input v-model="form.trangthai" :error="form.errors.trangthai" :disabled="$page.props.auth.user.role == 0">
                  <option :value="1">Đang làm việc</option>
                  <option :value="0">Đã nghỉ việc</option>
               </select-input>
             </div>

             <div v-if="!nhanvien.deleted_at && $page.props.auth.user.role == 2 && $page.props.auth.user.id != nhanvien.user_id" class="pt-4 mt-4 border-t border-gray-100">
               <button class="w-full py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-sm font-bold transition-colors" @click="destroy">
                 Xóa Hồ Sơ Này
               </button>
             </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-2">
        <form @submit.prevent="update">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center">
               <icon name="users" class="w-5 h-5 mr-2 text-indigo-500" /> Thông Tin Cá Nhân
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               <text-input v-model="form.sdt" :error="form.errors.sdt" label="Số điện thoại" :disabled="$page.props.auth.user.role == 0"/>
               <text-input v-model="form.cmnd" :error="form.errors.cmnd" label="CMND/CCCD" type="number" :disabled="$page.props.auth.user.role == 0"/>
               
               <select-input v-model="form.gioitinh" :error="form.errors.gioitinh" label="Giới tính" :disabled="$page.props.auth.user.role == 0">
                 <option :value="0">Nam</option> <option :value="1">Nữ</option>
               </select-input>
               
               <text-input v-model="form.ngaysinh" :error="form.errors.ngaysinh" type="date" label="Ngày sinh" :disabled="$page.props.auth.user.role == 0"/>
               
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
               <select-input v-model="form.phucap" :error="form.errors.phucap" label="Vị trí (Phòng ban -> Chức vụ)" class="md:col-span-2" :disabled="$page.props.auth.user.role == 0">
                 <option :value="null">-- Chọn vị trí --</option>
                 <option v-for="pc in phucap" :key="pc.id" :value="pc.id">{{ pc.tenpb }} -> {{ pc.tencv }}</option>
               </select-input>

               <text-input v-model="form.hesoluong" :error="form.errors.hesoluong" label="Hệ số lương" :disabled="$page.props.auth.user.role == 0"/>
               
               <select-input v-model="form.bacluong" :error="form.errors.bacluong" label="Bậc lương" :disabled="$page.props.auth.user.role == 0">
                  <option :value="null">-- Chọn --</option>
                  <option v-for="i in 10" :key="i" :value="i">Bậc {{ i }}</option>
               </select-input>

               <select-input v-model="form.bangcap" :error="form.errors.bangcap" label="Bằng cấp">
                 <option :value="null">-- Chọn --</option>
                 <option v-for="bc in bangcap" :key="bc.id" :value="bc.id">{{ bc.tenbc }}</option>
               </select-input>

               <select-input v-model="form.chuyenmon" :error="form.errors.chuyenmon" label="Chuyên môn">
                 <option :value="null">-- Chọn --</option>
                 <option v-for="cm in chuyenmon" :key="cm.id" :value="cm.id">{{ cm.tencm }}</option>
               </select-input>

               <select-input v-model="form.ngoaingu" :error="form.errors.ngoaingu" label="Ngoại ngữ">
                 <option :value="null">-- Chọn --</option>
                 <option v-for="ng in ngoaingu" :key="ng.id" :value="ng.id">{{ ng.tenng }}</option>
               </select-input>
            </div>
          </div>

          <div class="flex items-center justify-end">
            <loading-button :loading="form.processing" class="btn-indigo px-8 py-3 rounded-lg shadow-lg font-bold" type="submit">
              Lưu Thay Đổi
            </loading-button>
          </div>
        </form>
      </div>
    </div>

    <div class="mb-10">
      <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-2">
         <h2 class="text-xl font-bold text-gray-800">Bảo Hiểm</h2>
         <inertia-link v-if="$page.props.auth.user.role > 0" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center" :href="route('baohiem.create', nhanvien.id)">
            <span class="text-xl mr-1">+</span> Thêm Bảo Hiểm
         </inertia-link>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead class="bg-gray-50 border-b border-gray-100">
               <tr class="text-left font-bold text-gray-500 uppercase text-xs">
                  <th class="px-6 py-3">Mã số</th>
                  <th class="px-6 py-3">Loại BH</th>
                  <th class="px-6 py-3">Ngày cấp</th>
                  <th class="px-6 py-3">Ngày hết hạn</th>
                  <th class="px-6 py-3">Mức đóng</th>
                  <th class="px-6 py-3"></th>
               </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
               <tr v-for="bh in baohiem" :key="bh.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-3 font-medium text-gray-900">{{ bh.maso }}</td>
                  <td class="px-6 py-3 text-gray-600">{{ bh.tenbh }}</td>
                  <td class="px-6 py-3 text-gray-600">{{ bh.ngaycap }}</td>
                  <td class="px-6 py-3 text-gray-600">{{ bh.ngayhethan }}</td>
                  <td class="px-6 py-3 font-bold text-indigo-600">{{ bh.mucdong }}</td>
                  <td class="px-6 py-3 text-right">
                     <inertia-link :href="$page.props.auth.user.role > 0 ? route('baohiem.edit', bh.id) : '#'" class="text-gray-400 hover:text-indigo-600">
                        <icon name="cheveron-right" class="w-5 h-5" />
                     </inertia-link>
                  </td>
               </tr>
               <tr v-if="baohiem.length === 0">
                  <td colspan="6" class="px-6 py-6 text-center text-gray-400 italic">Chưa có thông tin bảo hiểm.</td>
               </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="mb-10">
      <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-2">
         <h2 class="text-xl font-bold text-gray-800">Hợp Đồng Lao Động</h2>
         <inertia-link v-if="$page.props.auth.user.role > 0" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center" :href="route('hopdong.create', nhanvien.id)">
            <span class="text-xl mr-1">+</span> Thêm Hợp Đồng
         </inertia-link>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead class="bg-gray-50 border-b border-gray-100">
               <tr class="text-left font-bold text-gray-500 uppercase text-xs">
                  <th class="px-6 py-3">Mã HĐ</th>
                  <th class="px-6 py-3">Loại Hợp Đồng</th>
                  <th class="px-6 py-3 text-indigo-600">Phân tích AI</th>
                  <th class="px-6 py-3">Ngày bắt đầu</th>
                  <th class="px-6 py-3">Ngày kết thúc</th>
                  <th class="px-6 py-3"></th>
               </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
               <tr v-for="hd in hopdong" :key="hd.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-3 font-medium text-gray-900">{{ hd.mahd }}</td>
                  <td class="px-6 py-3">
                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="hd.loaihopdong ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'">
                        {{ hd.loaihopdong ? 'Chính thức' : 'Thử việc' }}
                     </span>
                  </td>
                  <td class="px-6 py-3 text-gray-500 italic text-sm">--</td> <td class="px-6 py-3 text-gray-600">{{ hd.ngaybd }}</td>
                  <td class="px-6 py-3 text-gray-600">{{ hd.ngaykt }}</td>
                  <td class="px-6 py-3 text-right">
                     <inertia-link :href="$page.props.auth.user.role > 0 ? route('hopdong.edit', hd.id) : '#'" class="text-gray-400 hover:text-indigo-600">
                        <icon name="cheveron-right" class="w-5 h-5" />
                     </inertia-link>
                  </td>
               </tr>
               <tr v-if="hopdong.length === 0">
                  <td colspan="6" class="px-6 py-6 text-center text-gray-400 italic">Chưa có hợp đồng nào.</td>
               </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/StyledTextInput' 
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import Icon from '@/Shared/Icon'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo() {
    return { title: `Sửa: ${this.form.hovaten}` }
  },
  layout: Layout,
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    Icon,
    TrashedMessage,
  },
  props: {
    nhanvien: Object,
    phucap: Array,
    bangcap: Array,
    chuyenmon: Array,
    ngoaingu: Array,
    tongiao: Array,
    dantoc: Array,
    baohiem: Array,
    hopdong: Array
  },
  remember: 'form',
  data() {
    return {
      photoPreview: null,
      form: this.$inertia.form({
        _method: 'put',
        manv: this.nhanvien.manv,
        hovaten: this.nhanvien.hovaten,
        phucap: this.nhanvien.phucap,
        bangcap: this.nhanvien.bangcap,
        ngoaingu: this.nhanvien.ngoaingu,
        chuyenmon: this.nhanvien.chuyenmon,
        tongiao: this.nhanvien.tongiao,
        dantoc: this.nhanvien.dantoc,
        trangthai: this.nhanvien.trangthai,
        gioitinh: this.nhanvien.gioitinh,
        ngaysinh: this.nhanvien.ngaysinh,
        sdt: this.nhanvien.sdt,
        cmnd: this.nhanvien.cmnd,
        diachi: this.nhanvien.diachi,
        quequan: this.nhanvien.quequan,
        bacluong: this.nhanvien.bacluong,
        hesoluong: this.nhanvien.hesoluong ? this.nhanvien.hesoluong.toString() : '',
        photo: null,
      }),
    }
  },
  methods: {
    update() {
      // Logic gửi post để xử lý file đính kèm (photo) thay vì put trực tiếp
      this.form.post(this.route('nhanvien.update', this.nhanvien.id), {
        onSuccess: () => {
          this.photoPreview = null
          this.form.reset('photo')
        },
      })
    },
    destroy() {
      if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
        this.$inertia.delete(this.route('nhanvien.destroy', this.nhanvien.id))
      }
    },
    restore() {
       if (confirm('Bạn có chắc chắn muốn khôi phục hồ sơ này?')) {
          this.$inertia.put(this.route('nhanvien.restore', this.nhanvien.id))
       }
    },
    selectNewPhoto() {
      this.$refs.photo.click()
    },
    updatePhotoPreview() {
      const file = this.$refs.photo.files[0]
      if (!file) return
      
      const reader = new FileReader()
      reader.onload = (e) => {
        this.photoPreview = e.target.result
      }
      reader.readAsDataURL(file)
    },
  },
}
</script>