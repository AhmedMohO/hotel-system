<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { CalendarDate, DateFormatter, getLocalTimeZone, parseDate, today } from '@internationalized/date'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { Calendar } from '@/components/ui/calendar'
import { Button } from '@/components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { cn } from '@/lib/utils'

const props = defineProps<{
  modelValue?: string | null
  placeholder?: string
  disabled?: boolean
  class?: string
  minValue?: string
  maxValue?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | undefined): void
}>()

const df = new DateFormatter('en-US', {
  dateStyle: 'long',
})

const dateValue = computed({
  get: () => {
    if (!props.modelValue) return undefined
    try {
      return parseDate(props.modelValue)
    } catch (e) {
      return undefined
    }
  },
  set: (val) => {
    emit('update:modelValue', val?.toString())
  },
})

// For min/max values
const minDateValue = computed(() => props.minValue ? parseDate(props.minValue) : undefined)
const maxDateValue = computed(() => props.maxValue ? parseDate(props.maxValue) : undefined)

const isOpen = ref(false)
</script>

<template>
  <Popover v-model:open="isOpen">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        :class="cn(
          'w-full justify-start text-left font-normal',
          !modelValue && 'text-muted-foreground',
          props.class,
        )"
        :disabled="disabled"
      >
        <CalendarIcon class="mr-2 h-4 w-4" />
        {{ modelValue ? df.format(dateValue!.toDate(getLocalTimeZone())) : (placeholder || 'Pick a date') }}
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0">
      <Calendar
        v-model="dateValue"
        initial-focus
        :min-value="minDateValue"
        :max-value="maxDateValue"
        @update:model-value="isOpen = false"
      />
    </PopoverContent>
  </Popover>
</template>
