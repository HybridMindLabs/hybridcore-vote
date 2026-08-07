<script setup lang="ts">
import { ThumbsUp, Award } from '@lucide/vue';
import { computed } from 'vue';
import { useTheme } from '@/composables/useTheme';
import { useLocale } from '@/composables/useLocale';

defineProps<{ points: number; count: number }>();

const { theme } = useTheme();
const { t } = useLocale();
const dark = computed(() => theme.value === 'dark');
</script>

<template>
    <div class="rounded-2xl border overflow-hidden" :class="dark ? 'border-zinc-800/70 bg-[#111113]' : 'border-zinc-200 bg-white shadow-sm'">
        <div class="px-5 py-3.5 border-b flex items-center gap-2" :class="dark ? 'border-zinc-800/60 bg-[#1a1a1e]' : 'border-zinc-100 bg-zinc-50'">
            <ThumbsUp :size="14" :stroke-width="1.8" :class="dark ? 'text-blue-400' : 'text-blue-500'" />
            <p class="text-[13px] font-black" :class="dark ? 'text-zinc-100' : 'text-zinc-900'">{{ t('ext.vote.nav') }}</p>
        </div>
        <div class="flex">
            <div class="flex-1 px-5 py-4 text-center border-r" :class="dark ? 'border-zinc-800/60' : 'border-zinc-100'">
                <p class="text-2xl font-black text-amber-400 flex items-center justify-center gap-1">
                    <Award :size="18" :stroke-width="2" /> {{ points }}
                </p>
                <p class="text-[11px] uppercase tracking-wide mt-0.5" :class="dark ? 'text-zinc-500' : 'text-zinc-400'">{{ t('ext.vote.your_points') }}</p>
            </div>
            <div class="flex-1 px-5 py-4 text-center">
                <p class="text-2xl font-black" :class="dark ? 'text-zinc-100' : 'text-zinc-900'">{{ count }}</p>
                <p class="text-[11px] uppercase tracking-wide mt-0.5" :class="dark ? 'text-zinc-500' : 'text-zinc-400'">{{ t('ext.vote.your_votes') }}</p>
            </div>
        </div>
    </div>
</template>
