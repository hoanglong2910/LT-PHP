<template>
  <div class="py-6 space-y-2 text-white">
    <div v-for="item in menuItems" :key="item.label">
      <!-- HEADING -->
      <div v-if="item.isHeading && shouldShowItem(item)" class="px-6 mt-8 mb-3">
        <div class="h-px w-full bg-white/10 mb-3"></div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] !text-white/75">
          {{ item.label }}
        </p>
      </div>

      <!-- ITEM -->
      <div v-else-if="shouldShowItem(item)">
        <component
          :is="(item.children && item.children.length > 0) || !item.routeName ? 'div' : 'inertia-link'"
          :href="!(item.children && item.children.length > 0) && item.routeName ? route(item.routeName) : null"
          @click="item.children && item.children.length > 0 ? toggleSubmenu(item.label) : null"
          class="flex items-center justify-between py-3 px-6 cursor-pointer group relative border-l-4
                 transition-all duration-200 ease-out active:scale-[0.98]"
          :class="parentClass(item)"
        >
          <div class="flex items-center">
            <icon
              v-if="item.iconName"
              :name="item.iconName"
              class="w-5 h-5 mr-4 transition-colors duration-200"
              :class="iconTextClass(item)"
            />
            <span class="font-medium text-sm transition-colors duration-200" :class="labelTextClass(item)">
              {{ item.label }}
            </span>
          </div>

          <icon
            v-if="item.children && item.children.length > 0"
            name="cheveron-down"
            class="w-3 h-3 transition-transform duration-300"
            :class="chevronClass(item)"
          />
        </component>

        <!-- CHILDREN -->
        <div
          v-if="item.children && item.children.length > 0 && openParentLabel === item.label"
          class="mt-1 bg-black/20 py-2 shadow-inner"
        >
          <div v-for="child in item.children" :key="child.label">
            <inertia-link
              v-if="shouldShowItem(child)"
              :href="route(child.routeName)"
              class="flex items-center py-2.5 pl-14 pr-4 text-sm relative group/child
                     transition-all duration-200 ease-out rounded-lg mx-2 active:scale-[0.98]"
              :class="childClass(child)"
            >
              <!-- dot -->
              <span
                class="absolute left-10 w-1.5 h-1.5 rounded-full transition-all duration-300"
                :class="childDotClass(child)"
              ></span>

              <!-- optional ping ring when active -->
              <span
                v-if="isActive(child)"
                class="absolute left-[38px] w-3 h-3 rounded-full border border-white/40 animate-ping"
              ></span>

              <span>{{ child.label }}</span>
            </inertia-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {
  components: { Icon },
  data() {
    return {
      openParentLabel: null,
      menuItems: [
        { label: 'Tổng quan', isHeading: true, requiredRoleGT: 0 },
        {
          label: 'Dashboard',
          routeName: 'dashboard',
          iconName: 'dashboard',
          checkActive: (pageUrl) => pageUrl === '',
          requiredRoleGT: 0,
        },

        { label: 'Nhân sự', isHeading: true, requiredRoleGT: 0 },
        {
          label: 'Hồ sơ nhân viên',
          iconName: 'users',
          requiredRoleGT: 0,
          checkActive: (pageUrl) => pageUrl.startsWith('nhanvien') || pageUrl.startsWith('nghiviec'),
          children: [
            { label: 'Danh sách nhân viên', routeName: 'nhanvien', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('nhanvien') },
            { label: 'Đã nghỉ việc', routeName: 'nghiviec', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('nghiviec') },
          ],
        },
        {
          label: 'Hợp đồng & BH',
          iconName: 'hopdong',
          requiredRoleGT: 0,
          checkActive: (pageUrl) => pageUrl.startsWith('hopdong') || pageUrl.startsWith('baohiem') || pageUrl.startsWith('khautru'),
          children: [
            { label: 'Hợp đồng lao động', routeName: 'hopdong', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('hopdong') },
            { label: 'Bảo hiểm', routeName: 'baohiem', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('baohiem') },
            { label: 'Khấu trừ', routeName: 'khautru', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('khautru') },
          ],
        },

        { label: 'Lương thưởng', isHeading: true, requiredRoleGT: 0 },
        {
          label: 'Chấm công',
          iconName: 'chamcong',
          requiredRoleGT: 0,
          checkActive: (pageUrl) => pageUrl.startsWith('bangchamcong') || pageUrl.startsWith('thuongphat'),
          children: [
            { label: 'Bảng chấm công', routeName: 'bangchamcong', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('bangchamcong') },
            { label: 'Thưởng / Phạt', routeName: 'thuongphat', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('thuongphat') },
          ],
        },
        {
          label: 'Quản Lý Lương',
          iconName: 'nhanluong',
          requiredRoleGT: 0,
          checkActive: (pageUrl) => pageUrl.startsWith('nhanluong') || pageUrl.startsWith('ungluong'),
          children: [
            { label: 'Bảng lương tháng', routeName: 'nhanluong', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('nhanluong') },
            { label: 'Tạm ứng lương', routeName: 'ungluong', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('ungluong') },
          ],
        },

        { label: 'Cấu hình & Danh mục', isHeading: true, requiredRole: 2 },
        {
          label: 'Thiết lập lương',
          iconName: 'heso',
          requiredRole: 2,
          checkActive: (pageUrl) => pageUrl.startsWith('heso') || pageUrl.startsWith('phucap'),
          children: [
            { label: 'Hệ số lương', routeName: 'heso', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('heso') },
            { label: 'Các khoản phụ cấp', routeName: 'phucap', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('phucap') },
          ],
        },
        {
          label: 'Quản lý Danh mục',
          iconName: 'office',
          requiredRole: 2,
          checkActive: (pageUrl) => ['phongban','chucvu','bangcap','chuyenmon','ngoaingu','dantoc','tongiao','loaibaohiem'].some(x => pageUrl.startsWith(x)),
          children: [
            { label: 'Phòng ban', routeName: 'phongban', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('phongban') },
            { label: 'Chức vụ', routeName: 'chucvu', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('chucvu') },
            { label: 'Bằng cấp', routeName: 'bangcap', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('bangcap') },
            { label: 'Chuyên môn', routeName: 'chuyenmon', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('chuyenmon') },
            { label: 'Ngoại ngữ', routeName: 'ngoaingu', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('ngoaingu') },
            { label: 'Dân tộc', routeName: 'dantoc', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('dantoc') },
            { label: 'Tôn giáo', routeName: 'tongiao', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('tongiao') },
            { label: 'Loại bảo hiểm', routeName: 'loaibaohiem', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('loaibaohiem') },
          ],
        },
        {
          label: 'Hệ thống',
          iconName: 'users',
          requiredRole: 2,
          checkActive: (pageUrl) => pageUrl.startsWith('users'),
          children: [
            { label: 'Tài khoản người dùng', routeName: 'users', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('users') },
          ],
        },
      ],
    }
  },
  computed: {
    currentUserRole() {
      return this.$page.props.auth && this.$page.props.auth.user ? this.$page.props.auth.user.role : null
    },
    pageUrlWithoutSlash() {
      return this.$page && typeof this.$page.url === 'string' ? this.$page.url.substr(1) : ''
    },
  },
  methods: {
    shouldShowItem(item) {
      if (this.currentUserRole === null && (item.requiredRole !== undefined || item.requiredRoleGT !== undefined)) return false

      if (item.children && item.children.length > 0) {
        const canShowAnyChild = item.children.some(child => this.shouldShowItem(child))
        if (!canShowAnyChild) return false
      }

      if (item.requiredRole !== undefined && this.currentUserRole !== item.requiredRole) return false
      if (item.requiredRoleGT !== undefined && !(this.currentUserRole > item.requiredRoleGT)) return false
      return true
    },
    isActive(item) {
      return item.checkActive ? item.checkActive(this.pageUrlWithoutSlash) : false
    },
    toggleSubmenu(itemLabel) {
      this.openParentLabel = this.openParentLabel === itemLabel ? null : itemLabel
    },

    // ✅ Effects
    isOpen(item) {
      return item.children && this.openParentLabel === item.label
    },
    parentClass(item) {
      const isParentActive = this.isActive(item) && !item.children
      const isParentOpen = this.isOpen(item)

      if (isParentActive) {
        return 'border-white bg-gradient-to-r from-white/10 to-transparent shadow-[0_0_0_1px_rgba(255,255,255,0.10),0_12px_30px_rgba(0,0,0,0.35)]'
      }
      if (isParentOpen) {
        return 'border-white/60 bg-white/5 shadow-[0_0_0_1px_rgba(255,255,255,0.08)]'
      }
      return 'border-transparent hover:bg-white/5 hover:shadow-[0_0_0_1px_rgba(255,255,255,0.08)]'
    },
    labelTextClass(item) {
      const isParentActive = this.isActive(item) && !item.children
      const isParentOpen = this.isOpen(item)
      if (isParentActive || isParentOpen) return 'text-white'
      return 'text-white/85 group-hover:text-white'
    },
    iconTextClass(item) {
      const isParentActive = this.isActive(item) && !item.children
      const isParentOpen = this.isOpen(item)
      if (isParentActive || isParentOpen) return 'text-white'
      return 'text-white/80 group-hover:text-white'
    },
    chevronClass(item) {
      const opened = this.isOpen(item)
      return `${opened ? 'rotate-180 text-white' : 'text-white/70 group-hover:text-white'}`
    },
    childClass(child) {
      if (this.isActive(child)) {
        return 'text-white font-semibold bg-white/10 shadow-[0_0_0_1px_rgba(255,255,255,0.12),0_10px_20px_rgba(0,0,0,0.35)]'
      }
      return 'text-white/70 hover:text-white hover:bg-white/5'
    },
    childDotClass(child) {
      return this.isActive(child)
        ? 'bg-white shadow-[0_0_10px_rgba(255,255,255,0.95)] scale-[1.6]'
        : 'bg-white/35 group-hover/child:bg-white/70'
    },
  },
  mounted() {
    const activeItem = this.menuItems.find(item => item.children && this.isActive(item))
    if (activeItem) this.openParentLabel = activeItem.label
  },
}
</script>
