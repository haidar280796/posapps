<script setup lang="ts">
import { Store } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <Store color="#000" :class="className" class="fill-white" v-bind="$attrs" />
</template>
