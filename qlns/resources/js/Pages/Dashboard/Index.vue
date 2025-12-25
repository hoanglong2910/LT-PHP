<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Trang Chủ</h1>
    <div v-if="currentUserRole === 1 || currentUserRole === 2">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
      <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="flex items-center">
          <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
              Nhân viên đang làm việc
            </p>
            <p class="text-3xl font-semibold text-gray-700">
              {{ tongSoNhanVienDangLamViec }}
            </p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="flex items-center">
          <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
              Nhân viên mới (30 ngày)
            </p>
            <p class="text-3xl font-semibold text-gray-700">
              {{ soLuongNhanVienMoi }}
            </p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow-xl p-6">
        <div class="flex items-center">
          <div>
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">
              Nhân viên nghỉ (30 ngày)
            </p>
            <p class="text-3xl font-semibold text-gray-700">
              {{ soLuongNhanVienNghiViec }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="mb-10 bg-white rounded-lg shadow-md p-6">
      <h2 class="mb-4 text-2xl font-semibold text-gray-700">Tiến Trình Thanh Toán Lương (Tháng {{ currentMonth }}/{{ currentYear }})</h2>
      <div class="mb-4">
        <p class="text-md text-gray-600">
          Tổng số nhân viên công ty: <span class="font-semibold">{{ tongSoNhanVienDangLamViec }}</span>
        </p>
        <p class="text-md text-gray-600">
          Số lượng nhân viên đã thanh toán lương: <span class="font-semibold text-green-600">{{ soNhanVienDaThanhToanLuong }}</span>
        </p>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-6 mb-4">
        <div
          class="bg-indigo-600 h-6 rounded-full text-xs font-medium text-white text-center py-1 leading-none"
          :style="{ width: paymentProgressPercentage + '%' }"
        >
          {{ paymentProgressPercentage.toFixed(1) }}%
        </div>
      </div>
      <div class="mb-4">
         <p class="text-md text-gray-600">
          Tổng tiền đã ứng lương tháng này: <span class="font-semibold text-orange-600">{{ formatCurrency(tongTienUngLuongThangNay) }}</span>
        </p>
      </div>
      <div class="flex space-x-4">
        <inertia-link :href="route('nhanluong.index')" class="px-10 py-2 bg-gray-200 text-black rounded-md hover:bg-indigo-600 text-sm hover:text-white font-medium">
          Chi tiết Thanh toán Lương
        </inertia-link>
        <inertia-link :href="route('ungluong.index')" class="px-10 py-2 bg-gray-200 text-black rounded-md hover:bg-indigo-600 text-sm hover:text-white font-medium">
          Chi tiết Ứng Lương
        </inertia-link>
      </div>
    </div>
    <div class="mb-10 bg-white rounded-lg shadow-md p-6">
      <h2 class="mb-4 text-2xl font-semibold text-gray-700">Số Lượng Nhân Viên Theo Phòng Ban</h2>
      <div style="position: relative;  width:100%; ">
        <CustomBarChart
          v-if="chartDataReady.nvTheoPhongBan"
          :chart-data="nhanVienTheoPhongBanComputedChartData"
          :chart-options="barChartOptions"
          chart-id="nv-phong-ban-chart-standalone"
          dataset-id-key="nvphongban-standalone"
          />
          <p v-else class="text-gray-500 italic">Không có dữ liệu nhân viên theo phòng ban hoặc dữ liệu chưa sẵn sàng.</p>
      </div>
    </div>
      <div class="mb-10 bg-white rounded-lg shadow-md p-6">
        <h2 class="mb-4 text-xl font-semibold text-gray-700">Sinh Nhật Trong Tháng {{ currentMonth }}</h2>
        <div v-if="nhanVienSinhNhatThangNay && nhanVienSinhNhatThangNay.length > 0">
          <div class="space-y-3 overflow-y-auto" style="max-height: 320px;"> 
            <div v-for="nv in displayedBirthdayList" :key="nv.hovaten" class="py-2 px-1 border-b border-gray-100 last:border-b-0">
              <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-800">{{ nv.hovaten }}</p>
                <p class="text-xs text-gray-600 bg-sky-100 text-sky-700 px-2 py-1 rounded">Ngày: {{ nv.ngaysinh_formatted }}</p>
              </div>
            </div>
          </div>
          <div v-if="canShowMoreBirthdays" class="mt-4 text-center">
            <button
              @click="showMoreBirthdays"
              class="text-sm text-indigo-600 hover:text-indigo-800 font-medium focus:outline-none"
            >
              Xem thêm (còn {{ nhanVienSinhNhatThangNay.length - birthdayVisibleItemsCount }} người)
            </button>
          </div>
        </div>
        <p v-else class="text-gray-500 italic">Không có nhân viên nào có sinh nhật trong tháng này.</p>
      </div>
      
    <div class="mb-10 bg-white rounded-lg shadow-md p-6">
      <h2 class="mb-4 text-2xl font-semibold text-gray-700">Thống Kê & Báo Cáo Chi Tiết</h2>

      <div class="mb-6 flex flex-wrap gap-3"> 
        <button
          @click="selectDisplayType('bangCap')"
          :class="selectedDisplayType === 'bangCap' ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'"
          class="px-4 py-2 rounded-md font-medium text-sm shadow hover:shadow-lg transition-shadow"
        >
          Theo Bằng Cấp
        </button>
        <button
          @click="selectDisplayType('chuyenMon')"
          :class="selectedDisplayType === 'chuyenMon' ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'"
          class="px-4 py-2 rounded-md font-medium text-sm shadow hover:shadow-lg transition-shadow"
        >
          Theo Chuyên Môn
        </button>
        <button
          @click="selectDisplayType('ngoaiNgu')" 
          :class="selectedDisplayType === 'ngoaiNgu' ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'"
          class="px-4 py-2 rounded-md font-medium text-sm shadow hover:shadow-lg transition-shadow"
        >
          Theo Ngoại Ngữ
        </button>
        <button
          @click="selectDisplayType('topThuong')"
          :class="selectedDisplayType === 'topThuong' ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'"
          class="px-4 py-2 rounded-md font-medium text-sm shadow hover:shadow-lg transition-shadow"
        >
          Top 3 Thưởng
        </button>
        <button
          @click="selectDisplayType('topPhat')"
          :class="selectedDisplayType === 'topPhat' ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'"
          class="px-4 py-2 rounded-md font-medium text-sm shadow hover:shadow-lg transition-shadow"
        >
          Top 3 Phạt
        </button>
      </div>

      <div class="mt-6">
        <div v-if="selectedDisplayType === 'bangCap'">
          <h3 class="text-lg font-medium text-gray-600 mb-2">Phân loại theo Bằng Cấp</h3>
          <CustomPieChart
            v-if="chartDataReady.bangCap"
            :chart-data="bangCapChartData"
            :chart-options="chartOptionsWithDataLabels"
            chart-id="bang-cap-chart-selected"
            dataset-id-key="bangcap-selected"
            :width="400"
            :height="300" 
            style="position: relative; max-height:350px; width:100%"
          />
          <p v-else class="text-gray-500 italic">Không có dữ liệu bằng cấp hoặc dữ liệu chưa sẵn sàng.</p>
        </div>

        <div v-if="selectedDisplayType === 'chuyenMon'">
          <h3 class="text-lg font-medium text-gray-600 mb-2">Phân loại theo Chuyên Môn</h3>
          <CustomPieChart
            v-if="chartDataReady.chuyenMon"
            :chart-data="chuyenMonChartData"
            :chart-options="chartOptionsWithDataLabels"
            chart-id="chuyen-mon-chart-selected"
            dataset-id-key="chuyenmon-selected"
            :width="400"
            :height="300"
            style="position: relative; max-height:350px; width:100%"
          />
          <p v-else class="text-gray-500 italic">Không có dữ liệu chuyên môn hoặc dữ liệu chưa sẵn sàng.</p>
        </div>

        <div v-if="selectedDisplayType === 'ngoaiNgu'">
          <h3 class="text-lg font-medium text-gray-600 mb-2">Phân loại theo Ngoại Ngữ</h3>
          <CustomPieChart
            v-if="chartDataReady.ngoaiNgu"
            :chart-data="ngoaiNguChartData"
            :chart-options="chartOptionsWithDataLabels"
            chart-id="ngoai-ngu-chart-selected"
            dataset-id-key="ngoaingu-selected"
            :width="400"
            :height="300"
            style="position: relative; max-height:350px; width:100%"
          />
          <p v-else class="text-gray-500 italic">Không có dữ liệu ngoại ngữ hoặc dữ liệu chưa sẵn sàng.</p>
        </div>

        <div v-if="selectedDisplayType === 'topThuong'">
          <h3 class="text-xl font-semibold text-gray-700 mb-4">Top 3 Nhân Viên Được Thưởng Nhiều Nhất</h3>
          <div v-if="topNhanVienThuong && topNhanVienThuong.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hạng</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân Viên</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số Lần Thưởng</th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Chi tiết</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(item, index) in topNhanVienThuong" :key="`thuong-${item.nhanvien_id}`">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ index + 1 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.nhanvien.hovaten }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.so_lan_thuong }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <inertia-link :href="route('thuongphat.index', { search_nhanvien: item.nhanvien.hovaten, search_loai: 'thuong' })" class="text-indigo-600 hover:text-indigo-900">
                      Xem chi tiết
                    </inertia-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-else class="text-gray-500 italic">Chưa có dữ liệu khen thưởng.</p>
        </div>

        <div v-if="selectedDisplayType === 'topPhat'">
          <h3 class="text-xl font-semibold text-gray-700 mb-4">Top 3 Nhân Viên Bị Phạt Nhiều Nhất</h3>
          <div v-if="topNhanVienPhat && topNhanVienPhat.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hạng</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân Viên</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số Lần Phạt</th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Chi tiết</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(item, index) in topNhanVienPhat" :key="`phat-${item.nhanvien_id}`">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ index + 1 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.nhanvien.hovaten }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.so_lan_phat }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                     <inertia-link :href="route('thuongphat.index', { search_nhanvien: item.nhanvien.hovaten, search_loai: 'phat' })" class="text-indigo-600 hover:text-indigo-900">
                      Xem chi tiết
                    </inertia-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-else class="text-gray-500 italic">Chưa có dữ liệu kỷ luật/phạt.</p>
        </div>
        
        <p v-if="!selectedDisplayType && (nhanVienTheoBangCap || topNhanVienThuong)" class="text-gray-500 italic mt-4">Vui lòng chọn một mục để xem chi tiết.</p>
      </div>
      </div>
      
    </div>
    <div v-else-if="currentUserRole === 0" class="mt-6">
      <p class="text-lg text-gray-700">Chào mừng bạn đến với hệ thống!</p>
      </div>
      <div v-else class="mt-6">
        <p class="text-lg text-gray-600">Bạn không có quyền truy cập vào nội dung này.</p>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import { Pie as CustomPieChart, Bar as CustomBarChart } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale, BarElement, LinearScale } from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, BarElement, LinearScale, ChartDataLabels);


export default {
  metaInfo: { title: 'Dashboard' },
  layout: Layout,
  components: {
    CustomPieChart,
    CustomBarChart,
  },
  props: {
    tongSoNhanVienDangLamViec: Number,
    soLuongNhanVienMoi: Number,
    soLuongNhanVienNghiViec: Number,
    nhanVienTheoBangCap: Array,
    nhanVienTheoChuyenMon: Array,
    nhanVienTheoNgoaiNgu: Array,
    
    topNhanVienThuong: Array,
    topNhanVienPhat: Array,
    soNhanVienDaThanhToanLuong: Number,
    tongTienUngLuongThangNay: Number,
    nhanVienTheoPhongBanChart: Array,
    nhanVienSinhNhatThangNay: Array,
  },
  data() {
    return {
      
      selectedDisplayType: null,
      
      chartDataReady: {
        bangCap: false,
        chuyenMon: false,
        ngoaiNgu: false,
        nvTheoPhongBan: false,
      },
      currentMonth: new Date().getMonth() + 1, 
      currentYear: new Date().getFullYear(),
      birthdayItemsPerPage: 5,
      birthdayVisibleItemsCount: 5,
    }
  },
  computed: {
    nhanVienTheoPhongBanComputedChartData() {
      if (!this.nhanVienTheoPhongBanChart || this.nhanVienTheoPhongBanChart.length === 0) {
        return { labels: [], datasets: [{ data: [] }] };
      }
      const labels = this.nhanVienTheoPhongBanChart.map(item => item.ten_phong_ban);
      const data = this.nhanVienTheoPhongBanChart.map(item => item.so_luong_nhan_vien);
      return {
        labels: labels,
        datasets: [
          {
            label: 'Số lượng nhân viên',
            backgroundColor: '#42A5F5', // Một màu hoặc mảng màu
            borderColor: '#1E88E5',
            borderWidth: 1,
            data: data,
            barPercentage: 0.5, 
            categoryPercentage: 0.7, 
          }
        ]
      };
    },
    displayedBirthdayList() {
      if (!this.nhanVienSinhNhatThangNay) {
        return [];
      }
      return this.nhanVienSinhNhatThangNay.slice(0, this.birthdayVisibleItemsCount);
    },
    // Computed property để kiểm tra xem có thể "Xem thêm" sinh nhật không
    canShowMoreBirthdays() {
      if (!this.nhanVienSinhNhatThangNay) {
        return false;
      }
      return this.birthdayVisibleItemsCount < this.nhanVienSinhNhatThangNay.length;
    },
    currentUserRole() {
      return this.$page.props.auth && this.$page.props.auth.user ? this.$page.props.auth.user.role : null;
    },
    baseChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart',
        },
        indexAxis: 'x', 
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              color: '#374151',
              precision: 0
            },
            grid: { borderColor: '#D1D5DB', color: '#E5E7EB' }
          },
          x: {
            ticks: {
              color: '#374151',
              
              autoSkip: true,
              maxRotation: 70, 
              minRotation: 20 
            },
            grid: { display: false }
          }
        },
        plugins: {
          datalabels: { 
            display: true,
            color: '#FFFFFF',
            font: { weight: 'semibold', size: 10 }
          },
          legend: {
            position: 'top',
            labels: {
                font: { size: 12 },
                color: '#374151' 
            }
          },
          title: { display: false, },
        }
      };
    },
    chartOptionsWithDataLabels() {
      return {
        ...this.baseChartOptions,
        plugins: {
          ...this.baseChartOptions.plugins,
          datalabels: {
            formatter: (value, ctx) => {
              if (ctx.chart.data.labels && ctx.chart.data.datasets[0].data) {
                let sum = 0;
                let dataArr = ctx.chart.data.datasets[0].data;
                dataArr.map(dataVal => { sum += dataVal; });
                if (sum === 0) return '0%';
                let percentage = (value * 100 / sum).toFixed(1) + '%';
                return percentage;
              }
              return '';
            },
            color: '#fff', 
            font: { weight: 'bold', size: 12, }, 
            anchor: 'center',
            align: 'center',
          }
        }
      };
    },
    bangCapChartData() {
      return this.formatChartData(this.nhanVienTheoBangCap,true);
    },
    chuyenMonChartData() {
      return this.formatChartData(this.nhanVienTheoChuyenMon,true);
    },
    ngoaiNguChartData() {
      return this.formatChartData(this.nhanVienTheoNgoaiNgu,true);
    },
    paymentProgressPercentage() {
      if (this.tongSoNhanVienDangLamViec > 0 && this.soNhanVienDaThanhToanLuong !== undefined) {
        const percentage = (this.soNhanVienDaThanhToanLuong / this.tongSoNhanVienDangLamViec) * 100;
        return Math.min(Math.max(percentage, 0), 100); 
      }
      return 0;
    }
  },
  methods: {
    formatChartData(dataArray) {
      if (!dataArray || dataArray.length === 0) {
        return { labels: [], datasets: [{ data: [], backgroundColor: [] }] };
      }
      const labels = dataArray.map(item => item.ten);
      const data = dataArray.map(item => item.soluong);
      const backgroundColors = [
        '#4CAF50', '#2196F3', '#FFC107', '#F44336', '#9C27B0',
        '#00BCD4', '#8BC34A', '#FF9800', '#E91E63', '#607D8B',
        '#795548', '#FF5722', '#03A9F4', '#CDDC39', '#3F51B5'
      ].slice(0, data.length);
      return {
        labels: labels,
        datasets: [ { backgroundColor: backgroundColors.length > 0 ? backgroundColors : ['#E0E0E0'], data: data, } ]
      };
    },
    updateChartDataReadyStates() {
      this.chartDataReady.bangCap = !!(this.nhanVienTheoBangCap && this.nhanVienTheoBangCap.length > 0 && this.bangCapChartData.labels && this.bangCapChartData.labels.length > 0);
      this.chartDataReady.chuyenMon = !!(this.nhanVienTheoChuyenMon && this.nhanVienTheoChuyenMon.length > 0 && this.chuyenMonChartData.labels && this.chuyenMonChartData.labels.length > 0);
      this.chartDataReady.ngoaiNgu = !!(this.nhanVienTheoNgoaiNgu && this.nhanVienTheoNgoaiNgu.length > 0 && this.ngoaiNguChartData.labels && this.ngoaiNguChartData.labels.length > 0);
      this.chartDataReady.nvTheoPhongBan = !!(this.nhanVienTheoPhongBanChart && this.nhanVienTheoPhongBanChart.length > 0 && this.nhanVienTheoPhongBanComputedChartData.labels && this.nhanVienTheoPhongBanComputedChartData.labels.length > 0);
    },
    
    selectDisplayType(type) {
      this.selectedDisplayType = type;
    },
    formatCurrency(value) {
        if (typeof value !== 'number') {
            return '0 ₫';
        }
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    showMoreBirthdays() {
      this.birthdayVisibleItemsCount += this.birthdayItemsPerPage;
      // Không để vượt quá tổng số lượng
      if (this.birthdayVisibleItemsCount > this.nhanVienSinhNhatThangNay.length) {
        this.birthdayVisibleItemsCount = this.nhanVienSinhNhatThangNay.length;
      }
    }
  },
  watch: {
    nhanVienTheoBangCap: { handler: 'updateChartDataReadyStates', immediate: true },
    nhanVienTheoChuyenMon: { handler: 'updateChartDataReadyStates', immediate: true },
    nhanVienTheoNgoaiNgu: { handler: 'updateChartDataReadyStates', immediate: true },
    nhanVienTheoPhongBanChart: { handler: 'updateChartDataReadyStates', immediate: true },
  },
  mounted() {
    console.log('Dashboard Mounted - Props Bằng Cấp:', this.nhanVienTheoBangCap);
    console.log('Dashboard Mounted - Top Thưởng:', this.topNhanVienThuong);
    console.log('Dashboard Mounted - Top Phạt:', this.topNhanVienPhat);
    console.log('Dashboard Mounted - Số NV đã thanh toán:', this.soNhanVienDaThanhToanLuong);
    console.log('Dashboard Mounted - Tổng ứng lương:', this.tongTienUngLuongThangNay);
    console.log('Dashboard Mounted - NV theo Phòng Ban Chart Data (prop):', this.nhanVienTheoPhongBanChart);
  }
}
</script>