<template>
  <layout title="Quản lý Dự án">
    <div class="container mx-auto p-6 text-sm">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Danh sách Dự án & Tiến độ</h1>

      <div class="bg-white p-6 rounded-lg shadow mb-8 border-t-4 border-blue-500">
        <h2 class="font-bold mb-4 text-lg">Tạo dự án mới</h2>
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-2">
            <label class="block font-medium mb-1">Tên dự án:</label>
            <input v-model="form.ten_du_an" type="text" class="w-full border rounded p-2" placeholder="Nhập tên dự án...">
          </div>
          <div>
            <label class="block font-medium mb-1">Người phụ trách:</label>
            <select v-model="form.nhanvien_id" class="w-full border rounded p-2 text-sm">
              <option value="">-- Chọn nhân viên --</option>
              <option v-for="nv in nhanviens" :key="nv.id" :value="nv.id">{{ nv.ten }}</option>
            </select>
          </div>
          <div>
            <label class="block font-medium mb-1">Ngày bắt đầu:</label>
            <input v-model="form.ngay_bat_dau" type="date" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block font-medium mb-1">Tiến độ (%):</label>
            <input v-model="form.tien_do" type="number" min="0" max="100" class="w-full border rounded p-2">
          </div>
          <div>
            <label class="block font-medium mb-1">Trạng thái:</label>
            <select v-model="form.trang_thai" class="w-full border rounded p-2">
              <option value="Đang thực hiện">Đang thực hiện</option>
              <option value="Tạm dừng">Tạm dừng</option>
              <option value="Hoàn thành">Hoàn thành</option>
            </select>
          </div>
          <div class="md:col-span-3 text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Lưu dự án</button>
          </div>
        </form>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="pj in projects" :key="pj.id" class="bg-white p-5 rounded-lg shadow border border-gray-100">
          <div class="flex justify-between items-start mb-4">
            <h3 class="font-bold text-blue-700 text-lg uppercase">{{ pj.ten_du_an }}</h3>
            <span :class="pj.tien_do == 100 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="px-3 py-1 rounded-full text-xs font-semibold">
              {{ pj.trang_thai }}
            </span>
          </div>
          
          <div class="mb-4 text-gray-600">
            <p><strong>Phụ trách:</strong> {{ pj.nhanvien ? pj.nhanvien.hovaten : 'Chưa có' }}</p>
            <p><strong>Thời gian:</strong> {{ pj.ngay_bat_dau }} đến {{ pj.ngay_ket_thuc || '...' }}</p>
          </div>

          <div class="w-full bg-gray-200 rounded-full h-4">
            <div 
              class="h-4 rounded-full transition-all duration-500" 
              :style="{ width: pj.tien_do + '%', backgroundColor: getProgressColor(pj.tien_do) }"
            ></div>
          </div>
          <p class="text-right mt-1 text-xs font-bold">{{ pj.tien_do }}% hoàn thành</p>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
  components: { Layout },
  props: { projects: Array, nhanviens: Array },
  data() {
    return {
      form: {
        ten_du_an: '',
        nhanvien_id: '',
        ngay_bat_dau: new Date().toISOString().substr(0, 10),
        tien_do: 0,
        trang_thai: 'Đang thực hiện',
      }
    }
  },
  methods: {
    submit() {
      this.$inertia.post('/projects', this.form, {
        onSuccess: () => {
          this.form.ten_du_an = '';
          alert('Đã thêm dự án!');
        }
      })
    },
    getProgressColor(percent) {
      if (percent < 30) return '#ef4444'; // Đỏ
      if (percent < 70) return '#f59e0b'; // Vàng
      return '#10b981'; // Xanh lá
    }
  }
}
</script>