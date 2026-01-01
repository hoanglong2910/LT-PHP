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
                <option v-for="nv in nhanvien" :key="nv.id" :value="nv.id">
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
        <div class="flex justify-between items-center mb-4">
          <h2 class="font-bold text-gray-700">Sơ đồ hiệu suất nhân viên</h2>
          
          <div class="flex gap-2 items-center">
            <span class="text-sm text-gray-500">Xem theo:</span>
            <select v-model="filterThang" class="border p-1 rounded text-sm">
              <option v-for="t in 12" :key="t" :value="t">Tháng {{ t }}</option>
            </select>
            <select v-model="filterNam" class="border p-1 rounded text-sm">
              <option v-for="n in [2024, 2025, 2026]" :key="n" :value="n">Năm {{ n }}</option>
            </select>
          </div>
        </div>

        <div style="height: 300px;">
          <canvas id="kpiChart"></canvas>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
          <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <h3 class="text-red-700 font-bold mb-2 flex items-center">
              ⚠️ Cần chú ý (KPI < 50%)
            </h3>
            <ul>
              <li v-for="item in filteredKpiThap" :key="item.id" class="text-sm text-red-600 mb-1">
                • {{ item.nhanvien ? item.nhanvien.hovaten : 'N/A' }}: {{ item.chi_so_kpi }}%
              </li>
              <li v-if="filteredKpiThap.length === 0" class="text-xs text-gray-400 italic">Không có dữ liệu tháng này</li>
            </ul>
          </div>

          <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <h3 class="text-green-700 font-bold mb-2">🏆 Xuất sắc (KPI >= 80%)</h3>
            <ul>
              <li v-for="item in filteredKpiCao" :key="item.id" class="text-sm text-green-600 mb-1">
                • {{ item.nhanvien ? item.nhanvien.hovaten : 'N/A' }}: {{ item.chi_so_kpi }}%
              </li>
              <li v-if="filteredKpiCao.length === 0" class="text-xs text-gray-400 italic">Không có dữ liệu tháng này</li>
            </ul>
          </div>
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
    nhanvien: Array,
    kpis: Array,
    kpiThap: Array, // Danh sách tổng từ server
    kpiCao: Array,  // Danh sách tổng từ server
  },
  data() {
    return {
      form: {
        nhanvien_id: '',
        chi_so_kpi: '',
        thang: new Date().getMonth() + 1,
        nam: new Date().getFullYear(),
      },
      // Bộ lọc mặc định là tháng/năm hiện tại
      filterThang: new Date().getMonth() + 1,
      filterNam: new Date().getFullYear(),
      chartInstance: null
    }
  },
  // Computed dùng để lọc danh sách chữ (Cần chú ý/Xuất sắc) theo tháng đang chọn
  computed: {
    filteredKpiThap() {
      return this.kpis.filter(k => k.thang == this.filterThang && k.nam == this.filterNam && k.chi_so_kpi < 50);
    },
    filteredKpiCao() {
      return this.kpis.filter(k => k.thang == this.filterThang && k.nam == this.filterNam && k.chi_so_kpi >= 80);
    }
  },
  watch: {
    // Tự vẽ lại biểu đồ khi dữ liệu gốc thay đổi hoặc khi bạn đổi tháng/năm trên bộ lọc
    kpis: { handler() { this.renderChart(); }, deep: true },
    filterThang() { this.renderChart(); },
    filterNam() { this.renderChart(); }
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
        },
      })
    },
    renderChart() {
      const ctx = document.getElementById('kpiChart')
      if (!ctx) return

      // Lọc dữ liệu chỉ lấy của tháng và năm đang chọn để vẽ biểu đồ
      const dataForChart = this.kpis.filter(k => 
        k.thang == this.filterThang && k.nam == this.filterNam
      );

      if (this.chartInstance) {
        this.chartInstance.destroy()
      }

      this.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: dataForChart.map(k => k.nhanvien ? k.nhanvien.hovaten : 'N/A'),
          datasets: [{
            label: `Chỉ số KPI Tháng ${this.filterThang}/${this.filterNam} (%)`,
            data: dataForChart.map(k => k.chi_so_kpi),
            backgroundColor: '#4f46e5',
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { 
            y: { 
              beginAtZero: true, 
              max: 100,
              title: { display: true, text: 'Phần trăm (%)' }
            } 
          }
        }
      })
    }
  }
}
</script>