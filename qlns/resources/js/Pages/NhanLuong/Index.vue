<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Nhận Lương</h1>
    
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trạng thái xoá:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null">- Chưa chọn -</option>
          <option value="only">Đã xoá</option>
          <option value="with">Tất cả</option>
        </select>
        <label class="mt-4 block text-gray-700">Tháng nhận (Lọc):</label>
        <input v-model="form.ngayluong" class="mt-1 w-full form-input" type="month"/>
      </search-filter>
      <a v-if="form.ngayluong" :href="route('nhanluong.export', { ngayluong: form.ngayluong })" class="btn-indigo" target="_blank"><span>Export</span></a>
      <a v-else :href="route('nhanluong.export')" class="btn-indigo" target="_blank"><span>Export</span></a>
    </div>

    <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded shadow-sm">
        <h3 class="font-bold text-indigo-700 mb-2">Tính lương hàng loạt</h3>
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-auto">
                <label class="block text-gray-700 text-sm font-bold mb-1">Tháng tính lương:</label>
                <input v-model="formBulk.ngaynhan" type="month" class="form-input border-gray-300 rounded w-full" />
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-gray-700 text-sm font-bold mb-1">Ngày công chuẩn:</label>
                <input v-model="formBulk.ngaycongchuan" type="number" class="form-input border-gray-300 rounded w-32" placeholder="26" />
            </div>
            <button @click="tinhLuongHangLoat" class="btn-indigo h-10 flex items-center justify-center">
                <icon name="cheveron-right" class="w-4 h-4 fill-white mr-2" />
                Thực hiện tính toán
            </button>
        </div>
        <div class="mt-2 text-xs text-gray-500 italic">
            * Lưu ý: Hệ thống sẽ tự động tính toán và cập nhật lương cho TẤT CẢ nhân viên đang hoạt động trong tháng đã chọn.
        </div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Mã nhân viên</th>
          <th class="px-6 pt-6 pb-4">Họ và tên</th>
          <th class="px-6 pt-6 pb-4">Thực Lĩnh</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Tháng nhận</th>
        </tr>
        <tr v-for="ul in nhanluong.data" :key="ul.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('nhanluong.edit', ul.id)">
              {{ ul.manv }}
              <icon v-if="ul.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('nhanluong.edit', ul.id)">
              {{ ul.hovaten }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('nhanluong.edit', ul.id)" tabindex="-1">
              {{ ul.thuclinh }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('nhanluong.edit', ul.id)" tabindex="-1">
              {{ ul.ngaynhan }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('nhanluong.edit', ul.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="nhanluong.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">Không có nhận lương nào cả.</td>
        </tr>
      </table>
    </div>
    <pagination :links="nhanluong.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import pickBy from 'lodash/pickBy'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Nhận Lương' },
  layout: Layout,
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  props: {
    nhanluong: Object,
    filters: Object,
  },
  data() {
    return {
      // Dữ liệu cho bộ lọc tìm kiếm
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        ngayluong: this.filters.ngayluong,
      },
      // Dữ liệu cho form tính lương hàng loạt
      formBulk: {
        ngaynhan: new Date().toISOString().slice(0, 7), // Mặc định là tháng hiện tại (YYYY-MM)
        ngaycongchuan: 26 // Mặc định 26 ngày công
      }
    }
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('nhanluong', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    // Hàm xử lý tính lương hàng loạt
    tinhLuongHangLoat() {
        if (!this.formBulk.ngaynhan) {
            alert('Vui lòng chọn tháng tính lương.');
            return;
        }
        if (!this.formBulk.ngaycongchuan || this.formBulk.ngaycongchuan < 1) {
            alert('Vui lòng nhập ngày công chuẩn hợp lệ.');
            return;
        }

        if (confirm('BẠN CÓ CHẮC CHẮN?\n\nHành động này sẽ tính toán và ghi đè lương cho TOÀN BỘ nhân viên trong tháng ' + this.formBulk.ngaynhan + '.')) {
            // Gọi route nhanluong.storeAll (cần khai báo trong web.php)
            this.$inertia.post(this.route('nhanluong.storeAll'), this.formBulk, {
                onSuccess: () => {
                    // Sau khi thành công, có thể reload lại trang hoặc reset form nếu cần
                    // Inertia sẽ tự động hiển thị flash message từ Controller
                },
                onError: (errors) => {
                    console.log(errors);
                    alert('Có lỗi xảy ra, vui lòng kiểm tra lại.');
                }
            });
        }
    }
  },
}
</script>