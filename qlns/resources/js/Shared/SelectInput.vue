<template>
  <div :class="$attrs.class">
    <label v-if="label" class="form-label block mb-1 font-bold text-xs uppercase tracking-wide" :class="error ? 'text-red-600' : 'text-gray-700'" :for="id">
      {{ label }}:
    </label>
    
    <div class="relative">
      <select 
        :id="id" 
        ref="input" 
        v-model="selected" 
        v-bind="$attrs" 
        class="form-select w-full rounded-lg shadow-sm border transition-colors px-3 py-2 focus:ring focus:ring-opacity-50" 
        :class="error 
          ? 'border-red-500 text-red-900 focus:border-red-500 focus:ring-red-200' 
          : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 text-gray-700'"
      >
        <slot />
      </select>
      
      <div v-if="error" class="absolute inset-y-0 right-0 pr-8 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>

    <div v-if="error" class="mt-1 text-xs font-bold text-red-600 italic flex items-center">
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
      default() {
        return `select-input-${this._uid}`
      },
    },
    value: [String, Number, Boolean],
    label: String,
    error: String,
  },
  data() {
    return {
      selected: this.value,
    }
  },
  watch: {
    selected(selected) {
      this.$emit('input', selected)
    },
    value(val) {
      this.selected = val
    }
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
  },
}
</script>