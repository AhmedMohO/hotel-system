<script setup lang="ts">
import { X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    modelValue?: File | null;
    currentAvatar?: string | null;
    error?: string;
    removed?: boolean;
    name?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [file: File | null];
    'update:removed': [removed: boolean];
}>();

const preview = ref<string | null>(null);
const inputRef = ref<HTMLInputElement | null>(null);
const localError = ref<string | null>(null);

function resolveAvatarUrl(value?: string | null): string {
    if (!value) {
        return '/images/avatar.jpg';
    }

    if (
        value.startsWith('http') ||
        value.startsWith('/') ||
        value.startsWith('data:')
    ) {
        return value;
    }

    if (value.startsWith('storage/')) {
        return `/${value}`;
    }

    return `/storage/${value}`;
}

// Set initial preview from current avatar
watch(
    () => props.currentAvatar,
    (val) => {
        if (!props.removed) {
            preview.value = resolveAvatarUrl(val);
        }
    },
    { immediate: true },
);

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    localError.value = null;

    if (file) {
        if (
            !['image/jpeg', 'image/jpg'].includes(file.type) &&
            !file.name.match(/\.(jpg|jpeg)$/i)
        ) {
            target.value = '';
            localError.value = 'Only JPG/JPEG format is allowed.';

            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            target.value = '';
            localError.value = 'Avatar must not exceed 2MB.';

            return;
        }

        emit('update:modelValue', file);
        emit('update:removed', false);
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    } else {
        emit('update:modelValue', null);
    }
}

function triggerInput() {
    inputRef.value?.click();
}

function removeAvatar() {
    preview.value = '/images/avatar.jpg';

    if (inputRef.value) {
        inputRef.value.value = '';
    }

    emit('update:modelValue', null);
    emit('update:removed', true);
}
</script>

<template>
    <div class="flex flex-col items-center gap-3">
        <div class="relative">
            <button
                type="button"
                class="group relative h-24 w-24 overflow-hidden rounded-full border-2 border-dashed border-muted-foreground/30 transition hover:border-primary focus:ring-2 focus:ring-primary focus:outline-none"
                @click="triggerInput"
            >
                <img
                    :src="preview || '/images/avatar.jpg'"
                    alt="Avatar preview"
                    class="h-full w-full object-cover"
                />
                <div
                    class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition group-hover:opacity-100"
                >
                    <span class="text-xs font-medium text-white">Change</span>
                </div>
            </button>
            <button
                v-if="preview"
                type="button"
                @click="removeAvatar"
                class="absolute -top-1 -right-1 rounded-full bg-destructive p-1 text-destructive-foreground shadow-sm hover:bg-destructive/90 focus:ring-2 focus:ring-destructive focus:ring-offset-2 focus:outline-none"
                title="Remove photo"
            >
                <X class="h-4 w-4" />
            </button>
        </div>

        <input
            ref="inputRef"
            type="file"
            :name="name"
            accept=".jpg,.jpeg"
            class="hidden"
            @change="onFileChange"
        />
        <div class="text-center">
            <p v-if="error || localError" class="text-xs text-destructive">
                {{ error || localError }}
            </p>
            <p v-else class="text-xs text-muted-foreground">
                JPG/JPEG · Max 2MB
            </p>
        </div>
    </div>
</template>
