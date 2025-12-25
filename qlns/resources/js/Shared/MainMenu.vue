<template>
  <div>
    <div v-for="item in menuItems" :key="item.label" class="mb-1">
      <div v-if="shouldShowItem(item)">
        <component
          :is="(item.children && item.children.length > 0) || !item.routeName ? 'div' : 'inertia-link'"
          :href="!(item.children && item.children.length > 0) && item.routeName ? route(item.routeName) : null"
          @click="item.children && item.children.length > 0 ? toggleSubmenu(item.label) : null"
          class="flex items-center group py-4 pl-1 pr-2 rounded-md cursor-pointer font-medium"
          :class="{
            'bg-indigo-700 text-white': isActive(item) || openParentLabel === item.label,
            'text-indigo-300 hover:text-white hover:bg-indigo-700': !(isActive(item) || openParentLabel === item.label)
          }"
        >
          <icon
            v-if="item.iconName"
            :name="item.iconName"
            class="w-4 h-4 mr-2 flex-shrink-0 fill-white"
            
          />
          <span class="flex-grow whitespace-nowrap">{{ item.label }}</span>
          <icon
            v-if="item.children && item.children.length > 0"
            name="cheveron-down"
            class="w-3 h-3 flex-shrink-0 ml-4 transform transition-transform duration-200 fill-white"
            :class="{
              'rotate-180': openParentLabel === item.label
              
            }"
          />
        </component>

        <div
          v-if="item.children && item.children.length > 0 && openParentLabel === item.label"
          class="mt-1 ml-4 pl-3"
        >
          <div v-for="child in item.children" :key="child.label" class="mb-1">
            <inertia-link
              v-if="shouldShowItem(child)"
              :href="route(child.routeName)"
              class="flex items-center group py-2 px-3 rounded-md text-sm font-medium "
              :class="{
                'bg-indigo-600 text-white': isActive(child),
                'text-indigo-300 hover:text-white hover:bg-indigo-700': !isActive(child)
              }"
            >
              <icon
                v-if="child.iconName"
                :name="child.iconName"
                class="w-3 h-3 mr-4 flex-shrink-0 fill-white"
                
              />
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
  components: {
    Icon,
  },
  data() {
    return {
      openParentLabel: null,
      menuItems: [
        { label: 'TỔNG QUAN HỆ THỐNG', isHeading: true, requiredRoleGT: 0 },
        {
          label: 'Trang Chủ',
          routeName: 'dashboard',
          iconName: 'dashboard',
          checkActive: (pageUrl) => pageUrl === '',
          requiredRoleGT: 0,
        },
        { label: 'QUẢN LÝ THÔNG TIN', isHeading: true, requiredRoleGT: 0 },
        {
          label: 'Nhân Viên', // Mục cha - chỉ để toggle
          iconName: 'users',
          requiredRoleGT: 0,
          checkActive: (pageUrl) => pageUrl.startsWith('nhanvien') || pageUrl.startsWith('nghiviec'),
          children: [
            { label: 'Danh Sách Nhân Viên', routeName: 'nhanvien', iconName: 'users', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('nhanvien') },
            { label: 'Nghỉ Việc', routeName: 'nghiviec', iconName: 'nghiviec', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('nghiviec') },
          ],
        },
        {
          label: 'Hợp Đồng', // Mục cha - chỉ để toggle
          iconName: 'hopdong',
          requiredRoleGT: 0,
          checkActive: (pageUrl) => pageUrl.startsWith('hopdong') || pageUrl.startsWith('baohiem') || pageUrl.startsWith('khautru'),
          children: [
            { label: 'Danh sách Hợp Đồng', routeName: 'hopdong', iconName: 'hopdong', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('hopdong') },
            { label: 'Bảo Hiểm', routeName: 'baohiem', iconName: 'baohiem', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('baohiem') },
            { label: 'Khẩu Trừ', routeName: 'khautru', iconName: 'khautru', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('khautru') },
          ],
        },
        { label: 'CHÍNH SÁCH & TÀI CHÍNH', isHeading: true, requiredRoleGT: 0 },
        {
          label: 'Hệ Số', // Mục cha - chỉ để toggle
          iconName: 'heso',
          requiredRole: 2, // Vai trò để thấy mục cha này (hoặc sẽ được xác định bởi con)
          checkActive: (pageUrl) => pageUrl.startsWith('heso') || pageUrl.startsWith('phucap'),
          children: [
            { label: 'Danh sách Hệ Số', routeName: 'heso', iconName: 'heso', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('heso') },
            { label: 'Phụ Cấp', routeName: 'phucap', iconName: 'phucap', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('phucap') },
          ],
        },
        {
          label: 'Quản Lý Chấm Công', // Mục cha mới
          iconName: 'chamcong',
          requiredRoleGT: 0, // Parent role should be permissive enough if children have mixed roles
          checkActive: (pageUrl) => pageUrl.startsWith('bangchamcong') || pageUrl.startsWith('thuongphat'),
          children: [
            { label: 'Xem Bảng Chấm Công', routeName: 'bangchamcong', iconName: 'chamcong', requiredRole: 2, checkActive: (pageUrl) => pageUrl.startsWith('bangchamcong') },
            { label: 'Thưởng Phạt', routeName: 'thuongphat', iconName: 'thuongphat', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('thuongphat') },
          ],
        },
        {
          label: 'Quản Lý Lương', // Mục cha mới
          iconName: 'nhanluong',
          requiredRoleGT: 0,
          checkActive: (pageUrl) => pageUrl.startsWith('nhanluong') || pageUrl.startsWith('ungluong'),
          children: [
            { label: 'Nhận Lương', routeName: 'nhanluong', iconName: 'nhanluong', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('nhanluong') },
            { label: 'Ứng Lương', routeName: 'ungluong', iconName: 'ungluong', requiredRoleGT: 0, checkActive: (pageUrl) => pageUrl.startsWith('ungluong') },
          ],
        },
      ],
    }
  },
  computed: {
    currentUserRole() {
      return this.$page.props.auth && this.$page.props.auth.user ? this.$page.props.auth.user.role : null;
    },
    pageUrlWithoutSlash() {
        if (this.$page && typeof this.$page.url === 'string') {
            return this.$page.url.substr(1);
        }
        return '';
    }
  },
  methods: {
    shouldShowItem(item) {
      if (this.currentUserRole === null && (item.requiredRole !== undefined || item.requiredRoleGT !== undefined)) {
        return false;
      }
      if (item.children && item.children.length > 0) {
        const canShowAnyChild = item.children.some(child => this.shouldShowItem(child));
        if (!canShowAnyChild) return false;
        // If parent has its own specific role, check it too. Otherwise, if any child is visible, parent is.
        if (item.requiredRole !== undefined && this.currentUserRole !== item.requiredRole) return false;
        if (item.requiredRoleGT !== undefined && !(this.currentUserRole > item.requiredRoleGT)) return false;
        return true;
      }
      if (item.requiredRole !== undefined) {
        return this.currentUserRole === item.requiredRole;
      }
      if (item.requiredRoleGT !== undefined) {
        return this.currentUserRole > item.requiredRoleGT;
      }
      return true;
    },
    isActive(item) {
        if (item.checkActive) {
            return item.checkActive(this.pageUrlWithoutSlash);
        }
        return false;
    },
    toggleSubmenu(itemLabel) {
      if (this.openParentLabel === itemLabel) {
        this.openParentLabel = null;
      } else {
        this.openParentLabel = itemLabel;
      }
    },
  },
}
</script>