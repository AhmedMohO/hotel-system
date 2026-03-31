<script setup lang="ts">
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

defineProps<{
    countries?: { name: string }[];
    name?: string;
    placeholder?: string;
    required?: boolean;
    tabindex?: number | string;
}>();

const modelValue = defineModel<string>();
const open = ref(false);

function handleSelect(val: string) {
    modelValue.value = val;
    open.value = false;
}
</script>

<template>
    <div>
        <Popover v-model:open="open">
            <PopoverTrigger as-child>
                <Button
                    variant="outline"
                    role="combobox"
                    :aria-expanded="open"
                    class="w-full justify-between bg-white font-normal"
                    :tabindex="tabindex"
                >
                    <span class="truncate">
                        {{
                            modelValue
                                ? modelValue
                                : placeholder || 'Select country...'
                        }}
                    </span>
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>

            <PopoverContent class="w-[var(--radix-popover-trigger-width)] p-0">
                <Command>
                    <CommandInput class="h-9" placeholder="Search country..." />
                    <CommandEmpty>No country found.</CommandEmpty>
                    <CommandList>
                        <CommandGroup>
                            <CommandItem
                                v-for="country in countries"
                                :key="country.name"
                                :value="country.name"
                                @select="() => handleSelect(country.name)"
                            >
                                {{ country.name }}
                                <Check
                                    :class="
                                        cn(
                                            'ml-auto h-4 w-4',
                                            modelValue === country.name
                                                ? 'opacity-100'
                                                : 'opacity-0',
                                        )
                                    "
                                />
                            </CommandItem>
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>

        <!-- Hidden input for standard form submission (e.g., in Register.vue) -->
        <input
            v-if="name"
            type="hidden"
            :name="name"
            :value="modelValue"
            :required="required"
        />
    </div>
</template>
