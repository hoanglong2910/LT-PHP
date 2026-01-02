<template>
  <div>
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Danh Sách Nhân Viên</h1>
        <p class="text-sm text-gray-500 mt-1">Quản lý hồ sơ, trạng thái làm việc và thông tin liên hệ.</p>
      </div>
      <div class="mt-4 md:mt-0 flex gap-3">
         <button @click="openModal" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <icon name="office" class="w-4 h-4 mr-2 text-green-600"/> Import Excel
         </button>
         <a :href="route('nhanvien.export')" target="_blank" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Export
         </a>
         <inertia-link :href="route('nhanvien.create')" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition-colors font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Thêm Mới
         </inertia-link>
      </div>
    </div>

    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col lg:flex-row gap-4 items-center justify-between">
      <div class="w-full lg:w-1/3 relative">
         <icon name="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
         <input 
            v-model="form.search" 
            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" 
            placeholder="Tìm theo Mã NV, Tên, Email..." 
         />
      </div>

      <div class="flex flex-wrap gap-3 w-full lg:w-auto justify-end">
         <select v-model="form.gioitinh" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
            <option :value="null">-- Giới tính --</option>
            <option value="nam">Nam</option>
            <option value="nu">Nữ</option>
         </select>
         
         <select v-model="form.trangthai" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
            <option :value="null">-- Trạng thái làm việc --</option>
            <option value="danglamviec">Đang làm việc</option>
            <option value="danghilam">Đã nghỉ làm</option>
         </select>

         <select v-model="form.trashed" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 text-red-600">
            <option :value="null">Thùng rác: Không</option>
            <option value="with">Hiện cả đã xóa</option>
            <option value="only">Chỉ hiện đã xóa</option>
         </select>
         
         <button @click="reset" class="text-sm text-gray-500 hover:text-indigo-600 underline px-2">Xóa lọc</button>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr class="text-left font-semibold text-gray-600 uppercase text-xs tracking-wider">
              <th class="px-6 py-4">Nhân viên</th>
              <th class="px-6 py-4">Thông tin liên hệ</th>
              <th class="px-6 py-4">Giới tính</th>
              <th class="px-6 py-4 text-center">Trạng thái</th>
              <th class="px-6 py-4 text-right">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="nv in nhanvien.data" :key="nv.id" class="hover:bg-indigo-50/40 transition-colors group">
              
              <td class="px-6 py-4">
                <div class="flex items-center">
                   <div class="flex-shrink-0 h-10 w-10">
                      <img v-if="nv.photo" class="h-10 w-10 rounded-full object-cover border border-gray-200 shadow-sm" :src="nv.photo" :alt="nv.hovaten">
                      <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                         {{ nv.hovaten.charAt(0).toUpperCase() }}
                      </div>
                   </div>
                   <div class="ml-4">
                      <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">
                        {{ nv.hovaten }}
                      </div>
                      <div class="text-xs text-gray-500 font-mono bg-gray-100 inline-block px-1.5 rounded mt-0.5">
                        {{ nv.manv }}
                      </div>
                   </div>
                </div>
              </td>

              <td class="px-6 py-4">
                 <div class="flex flex-col">
                    <span class="text-sm text-gray-700 flex items-center mb-1">
                       <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                       {{ nv.email }}
                    </span>
                    <span class="text-sm text-gray-500 flex items-center">
                       <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                       {{ nv.sdt }}
                    </span>
                 </div>
              </td>

              <td class="px-6 py-4">
                 <span v-if="nv.gioitinh == 0" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                    Nam
                 </span>
                 <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-50 text-pink-700 border border-pink-100">
                    Nữ
                 </span>
              </td>

              <td class="px-6 py-4 text-center">
                 <div v-if="nv.deleted_at" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                    <span class="w-1.5 h-1.5 mr-1.5 bg-red-600 rounded-full"></span> Đã xóa
                 </div>
                 <div v-else-if="nv.trangthai == 1" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    <span class="w-1.5 h-1.5 mr-1.5 bg-green-600 rounded-full"></span> Đang làm
                 </div>
                 <div v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                    <span class="w-1.5 h-1.5 mr-1.5 bg-gray-500 rounded-full"></span> Đã nghỉ
                 </div>
              </td>

              <td class="px-6 py-4 text-right">
                <inertia-link 
                    :href="route('nhanvien.edit', nv.id)" 
                    class="inline-block p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors"
                    title="Xem chi tiết & Sửa"
                >
                   <icon name="cheveron-right" class="w-5 h-5" />
                </inertia-link>
              </td>
            </tr>

            <tr v-if="nhanvien.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-500" colspan="5">
                 <div class="flex flex-col items-center justify-center">
                    <div class="p-4 rounded-full bg-gray-50 mb-3">
                        <icon name="search" class="w-8 h-8 text-gray-300" />
                    </div>
                    <p class="font-medium">Không tìm thấy nhân viên nào.</p>
                    <button @click="reset" class="text-sm text-indigo-600 mt-2 hover:underline">Xóa bộ lọc để xem tất cả</button>
                 </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-6">
       <pagination :links="nhanvien.links" />
    </div>

    <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-800">Import Nhân Viên</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="px-6 py-6">
           <form @submit.prevent="storeImport">
              <div class="mb-4">
                 <label class="block text-sm font-medium text-gray-700 mb-2">Chọn file Excel (.xlsx, .xls)</label>
                 <file-input v-model="form.fileimport" class="w-full" type="file" />
                 <p class="text-xs text-gray-500 mt-2">Vui lòng sử dụng file theo mẫu quy định.</p>
              </div>

              <div class="flex justify-end gap-3 mt-6">
                 <button type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm transition-colors" @click="closeModal">Hủy bỏ</button>
                 <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm shadow-md transition-colors flex items-center">
                    <span v-if="isImporting" class="mr-2">Đang tải...</span>
                    <span>Tải lên</span>
                 </button>
              </div>
           </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios';
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import FileInput from '@/Shared/FileInput'

export default {
  metaInfo: { title: 'Quản lý Nhân Viên' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    FileInput,
  },
  props: {
    nhanvien: Object, // Giữ nguyên tên prop theo code cũ của bạn
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        gioitinh: this.filters.gioitinh,
        trangthai: this.filters.trangthai,
        fileimport: null,
      },
      showImportModal: false, // Dùng biến này để điều khiển Modal thay vì class JS
      isImporting: false,
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        // Loại bỏ fileimport ra khỏi query string khi filter
        const { fileimport, ...queryForm } = this.form;
        let query = pickBy(queryForm);
        this.$inertia.replace(this.route('nhanvien', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    storeImport() {
      if (!this.form.fileimport) {
         alert('Vui lòng chọn file trước!');
         return;
      }
      this.isImporting = true;
      console.log(this.form.fileimport);
      var formData = new FormData();
      formData.append('file_import', this.form.fileimport);
      
      axios.post('/nhanvien/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
      }).then(() => {
        alert('Đã upload file thành công');
        this.closeModal();
        window.location.reload();
      })
      .catch(() => {
        alert('Đã xảy ra lỗi! Vui lòng thử lại.');
      })
      .finally(() => {
         this.isImporting = false;
      });
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    openModal() {
      this.showImportModal = true;
    },
    closeModal() {
      this.showImportModal = false;
      this.form.fileimport = null; // Reset file khi đóng
    }
  },
}
</script>