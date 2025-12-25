<template>
  <div :class="$attrs.class">
    <label v-if="label" :class="['block text-sm font-medium mb-1', labelTextClass]" :for="id">
      {{ label }}
      
    </label>
    <input
      :id="id"
      ref="input"
      v-bind="{ ...$attrs, class: null }" :class="[
        'block w-full shadow-sm sm:text-sm rounded-md px-3 py-2', 
        inputBorderClass,                       
        inputBgClass,                          
        inputTextClass,                         
        placeholder,   
        { 'cursor-not-allowed opacity-75': $attrs.disabled } 
      ]"
      :type="type"
      :value="value" @input="$emit('input', $event.target.value)" />
    <div v-if="error" class="mt-1 text-xs text-red-400">{{ error }}</div>
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
    error: String,
    required: { type: Boolean, default: false },

    
    labelTextClass: { type: String, default: 'text-gray-700' }, 
    inputBgClass: { type: String, default: 'bg-white' },        
    inputTextClass: { type: String, default: 'text-gray-900' },  
    inputBorderClass: { 
        type: String,
        default: 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
    },
    placeholderTextClass: { type: String, default: 'placeholder-gray-400' }
  },
  computed: {
    labelTextClassInternal() {
      return this.error ? 'text-red-600' : this.labelTextClass;
    },
    inputBgClassComputed() {
      return this.$attrs.disabled ? 'bg-gray-100' : this.inputBgClass;
    },
    inputTextClassComputed() {
      return this.$attrs.disabled ? 'text-gray-500' : this.inputTextClass; 
    },
    inputBorderClassComputed() {
      if (this.$attrs.disabled) {
        return 'border-gray-300'; 
      }
      return this.error
        ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
        : this.inputBorderClass;
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