<template>
  <layout title="Quản lý Dự án">
    <div class="container mx-auto p-6 text-sm">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Danh sách Dự án & Tiến độ</h1>

      <div v-if="$page.props.auth.user.role === 'admin'" 
           class="bg-white p-6 rounded-lg shadow mb-8 border-t-4 border-blue-500">
        <h2 class="font-bold mb-4 text-lg text-blue-700">Tạo dự án mới (Dành cho Quản lý)</h2>
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-2">
            <label class="block font-medium mb-1 italic">Tên dự án:</label>
            <input v-model="form.ten_du_an" type="text" class="w-full border rounded p-2 focus:ring-2 focus:ring-blue-400" placeholder="Nhập tên dự án...">
          </div>
          <div>
            <label class="block font-medium mb-1 italic">Người phụ trách:</label>
            <select v-model="form.nhanvien_id" class="w-full border rounded p-2 text-sm bg-gray-50">
              <option value="">-- Chọn nhân viên --</option>
              <option v-for="nv in nhanvien" :key="nv.id" :value="nv.id">{{ nv.ten }}</option>
            </select>
          </div>
          <div>
            <label class="block font-medium mb-1 italic">Ngày bắt đầu:</label>
            <input v-model="form.ngay_bat_dau" type="date" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block font-medium mb-1 italic">Ngày kết thúc (Dự kiến):</label>
            <input v-model="form.ngay_ket_thuc" type="date" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block font-medium mb-1 italic">Tiến độ hiện tại (%):</label>
            <input v-model="form.tien_do" type="number" min="0" max="100" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block font-medium mb-1 italic">Trạng thái:</label>
            <select v-model="form.trang_thai" class="w-full border rounded p-2">
              <option value="Đang thực hiện">Đang thực hiện</option>
              <option value="Tạm dừng">Tạm dừng</option>
              <option value="Hoàn thành">Hoàn thành</option>
            </select>
          </div>
          <div class="md:col-span-2 text-right flex items-end justify-end">
             <p class="text-xs text-gray-500 mr-4 mb-2">* Chế độ Quản trị viên</p>
             <button type="submit" 
                     style="background-color: #2563eb; color: #ffffff !important;"
                     class="px-8 py-2 rounded shadow hover:bg-blue-700 transition font-bold uppercase">
                Lưu dự án
             </button>
          </div>
        </form>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="pj in projects" :key="pj.id" class="bg-white p-5 rounded-lg shadow-md border-l-4" :style="{ borderLeftColor: getProgressColor(pj.tien_do) }">
          <div class="flex justify-between items-start mb-4">
            <h3 class="font-bold text-gray-800 text-lg uppercase">{{ pj.ten_du_an }}</h3>
            <span :class="pj.tien_do == 100 ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'" class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm">
              {{ pj.trang_thai }}
            </span>
          </div>
          
          <div class="mb-4 text-gray-600 space-y-1">
            <p class="flex justify-between"><span>👤 Phụ trách:</span> <span class="font-bold text-gray-800">{{ pj.nhanvien ? pj.nhanvien.hovaten : 'Chưa chỉ định' }}</span></p>
            <p class="flex justify-between"><span>📅 Bắt đầu:</span> <span>{{ pj.ngay_bat_dau }}</span></p>
            <p class="flex justify-between"><span>🏁 Kết thúc:</span> <span>{{ pj.ngay_ket_thuc || 'Chưa xác định' }}</span></p>
          </div>

          <div class="mt-4">
            <div class="flex justify-between text-xs font-bold mb-1">
              <span>Tiến độ</span>
              <span>{{ pj.tien_do }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
              <div 
                class="h-3 rounded-full transition-all duration-1000" 
                :style="{ width: pj.tien_do + '%', backgroundColor: getProgressColor(pj.tien_do) }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
  components: { Layout },
  props: { projects: Array, nhanvien: Array },
  data() {
    return {
      form: {
        ten_du_an: '',
        nhanvien_id: '',
        ngay_bat_dau: new Date().toISOString().substr(0, 10),
        ngay_ket_thuc: '',
        tien_do: 0,
        trang_thai: 'Đang thực hiện',
      }
    }
  },
  methods: {
    submit() {
      // Chặn thêm một lần nữa ở phía Javascript cho chắc chắn
      if (this.$page.props.auth.user.role !== 'admin') {
        alert('Bạn không có quyền thực hiện hành động này!');
        return;
      }

      this.$inertia.post('/projects', this.form, {
        onSuccess: () => {
          this.form.ten_du_an = '';
          this.form.ngay_ket_thuc = '';
          this.form.tien_do = 0;
          alert('Hệ thống đã cập nhật dự án mới!');
        },
        onError: (errors) => {
          alert('Lỗi: ' + Object.values(errors)[0]);
        }
      })
    },
    getProgressColor(percent) {
      if (percent < 30) return '#ef4444';
      if (percent < 100) return '#3b82f6';
      return '#10b981';
    }
  }
}
</script>