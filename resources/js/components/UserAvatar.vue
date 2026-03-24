<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';

const props = defineProps<{
    user: {
        name: string;
        avatar_image?: string | null;
        avatar?: string | null;
        [key: string]: any;
    };
}>();

const { getInitials } = useInitials();

const avatarUrl = computed(() => {
    const img = props.user.avatar_image || props.user.avatar;

    if (img) {
        return img.startsWith('http') || img.startsWith('/')
            ? img
            : `/storage/${img}`;
    }

    return '/images/avatar.jpg';
});
</script>

<template>
    <Avatar>
        <AvatarImage :src="avatarUrl" :alt="user.name" class="object-cover" />
        <AvatarFallback class="bg-muted text-foreground font-semibold">
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>
</template>
