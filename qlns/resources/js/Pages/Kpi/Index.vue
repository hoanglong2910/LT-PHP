<template>
  <layout title="Quản lý KPI">
    <div class="container mx-auto p-6">
      <h1 class="text-2xl font-bold mb-4">Quản lý & Thống kê KPI</h1>

      <div class="bg-white p-6 rounded shadow-md mb-8">
        <h2 class="font-bold mb-4 text-gray-700">Nhập chỉ số mới</h2>
        <form @submit.prevent="submit">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block mb-1 text-sm">Nhân viên:</label>
              <select v-model="form.nhanvien_id" class="w-full border p-2 rounded text-sm">
  <option value="" disabled>-- Chọn nhân viên --</option>
  <option v-for="nv in nhanviens" :key="nv.id" :value="nv.id">
    {{ nv.ten }}
  </option>
</select>
            </div>
            <div>
              <label class="block mb-1 text-sm">Chỉ số (%):</label>
              <input type="number" v-model="form.chi_so_kpi" class="w-full border p-2 rounded text-sm" placeholder="0-100">
            </div>
            <div>
              <label class="block mb-1 text-sm">Tháng/Năm:</label>
              <div class="flex gap-2">
                <input type="number" v-model="form.thang" class="w-1/2 border p-2 rounded text-sm" min="1" max="12">
                <input type="number" v-model="form.nam" class="w-1/2 border p-2 rounded text-sm">
              </div>
            </div>
            <div class="flex items-end">
              <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-medium">Lưu dữ liệu</button>
            </div>
          </div>
        </form>
      </div>

      <div class="bg-white p-6 rounded shadow-md mb-8">
        <h2 class="font-bold mb-4 text-gray-700">Sơ đồ hiệu suất nhân viên</h2>
        <div style="height: 300px;">
          <canvas id="kpiChart"></canvas>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout'
import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

export default {
  components: { Layout },
  props: {
    nhanviens: Array,
    kpis: Array,
  },
  watch: {
    // Mỗi khi danh sách kpis thay đổi (do server gửi về sau khi lưu), 
    // nó sẽ tự động chạy lại hàm vẽ biểu đồ
    kpis: {
      handler() {
        this.renderChart();
      },
      deep: true,
    },
  },
  data() {
    return {
      form: {
        nhanvien_id: '',
        chi_so_kpi: '',
        thang: new Date().getMonth() + 1,
        nam: new Date().getFullYear(),
      },
      chartInstance: null
    }
  },
  mounted() {
    this.renderChart()
  },
  methods: {
    submit() {
      this.$inertia.post('/kpi', this.form, {
        onSuccess: () => {
          this.form.chi_so_kpi = ''
          alert('Lưu KPI thành công!')
          // Sau khi lưu, trang sẽ tự tải lại và biểu đồ sẽ cập nhật
        },
      })
    },
    renderChart() {
      const ctx = document.getElementById('kpiChart')
      if (!ctx || this.kpis.length === 0) return

      if (this.chartInstance) {
        this.chartInstance.destroy()
      }

      this.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: this.kpis.map(k => k.nhanvien ? k.nhanvien.hovaten : 'N/A'),
          datasets: [{
            label: 'KPI (%)',
            data: this.kpis.map(k => k.chi_so_kpi),
            backgroundColor: '#4f46e5',
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true, max: 100 } }
        }
      })
    }
  }
}
</script>