<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ThumbsUp, ExternalLink } from '@lucide/vue';
import { computed } from 'vue';
import { useTheme } from '@/composables/useTheme';
import { useLocale } from '@/composables/useLocale';

const { theme } = useTheme();
const { t } = useLocale();
const dark = computed(() => theme.value === 'dark');
</script>

<template>
    <div class="text-center py-4">
        <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
            :class="dark ? 'bg-blue-500/12 text-blue-400' : 'bg-blue-50 text-blue-500'">
            <ThumbsUp :size="26" :stroke-width="1.8" />
        </div>
        <p class="text-[15px] font-black mb-1.5" :class="dark ? 'text-zinc-100' : 'text-zinc-900'">
            {{ t('ext.vote.onboarding_title') }}
        </p>
        <p class="text-[13px] leading-relaxed max-w-sm mx-auto mb-5" :class="dark ? 'text-zinc-500' : 'text-zinc-500'">
            {{ t('ext.vote.onboarding_body') }}
        </p>
        <Link :href="route('vote.index')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-[13px] font-bold bg-blue-500 text-white hover:bg-blue-600 transition-colors">
            <ExternalLink :size="14" :stroke-width="2" /> {{ t('ext.vote.vote_now') }}
        </Link>
    </div>
</template>
