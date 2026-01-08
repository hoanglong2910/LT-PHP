<template>
  <div :class="$attrs.class">
    <label v-if="label" :class="['block text-sm font-bold mb-1 uppercase tracking-wide', labelTextClassInternal]" :for="id">
      {{ label }}:
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <input
      :id="id"
      ref="input"
      v-bind="{ ...$attrs, class: null }"
      :class="[
        'block w-full shadow-sm sm:text-sm rounded-lg px-3 py-2 transition-colors border', 
        inputBorderClassComputed,      
        inputBgClassComputed,            
        inputTextClassComputed,          
        placeholderTextClass,    
        { 'cursor-not-allowed opacity-75': $attrs.disabled } 
      ]"
      :type="type"
      :value="value"
      @input="$emit('input', $event.target.value)" 
    />

    <div v-if="error" class="mt-1 text-xs font-bold text-red-600 italic flex items-center">
       <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
       {{ error }}
    </div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() { return `text-input-${this._uid}` },
    },
    type: { type: String, default: 'text' },
    value: [String, Number],
    label: String,
    error: String, // Nhận thông báo lỗi từ Controller
    required: { type: Boolean, default: false },

    // Các class style mặc định
    labelTextClass: { type: String, default: 'text-gray-700' }, 
    inputBgClass: { type: String, default: 'bg-white' },        
    inputTextClass: { type: String, default: 'text-gray-900' },  
    inputBorderClass: { 
        type: String,
        default: 'border-gray-300 focus:ring focus:ring-indigo-200 focus:border-indigo-500'
    },
    placeholderTextClass: { type: String, default: 'placeholder-gray-400' }
  },
  computed: {
    // Logic đổi màu LABEL khi có lỗi
    labelTextClassInternal() {
      return this.error ? 'text-red-600' : this.labelTextClass;
    },
    // Logic đổi màu NỀN khi disabled
    inputBgClassComputed() {
      return this.$attrs.disabled ? 'bg-gray-100' : this.inputBgClass;
    },
    // Logic đổi màu CHỮ khi disabled
    inputTextClassComputed() {
      return this.$attrs.disabled ? 'text-gray-500' : this.inputTextClass; 
    },
    // Logic đổi màu VIỀN khi có lỗi
    inputBorderClassComputed() {
      if (this.$attrs.disabled) {
        return 'border-gray-300'; 
      }
      return this.error
        ? 'border-red-500 focus:ring-red-200 focus:border-red-500' // Đỏ khi lỗi
        : this.inputBorderClass; // Mặc định khi không lỗi
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>