<template>
    <span
        :class="badgeClass"
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold tracking-wide"
    >
        <span class="h-1.5 w-1.5 rounded-full" :class="dotClass"></span>
        {{ label }}
    </span>
</template>

<script setup lang="ts">
const props = defineProps({
    approved: {
        type: Boolean,
        default: false,
    },
});

const badgeClass = props.approved
    ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
    : 'bg-amber-100 text-amber-700 border border-amber-200';

const dotClass = props.approved ? 'bg-emerald-500' : 'bg-amber-500';
const label = props.approved ? 'Approved' : 'Pending';
</script>
