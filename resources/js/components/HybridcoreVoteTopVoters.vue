<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Trophy } from '@lucide/vue';
import { computed } from 'vue';
import { useTheme } from '@/composables/useTheme';
import { useLocale } from '@/composables/useLocale';

interface Voter { username: string; avatar: string | null; votes: number }
defineProps<{ voters: Voter[] }>();

const { theme } = useTheme();
const { t } = useLocale();
const dark = computed(() => theme.value === 'dark');
</script>

<template>
    <div v-if="voters.length" class="rounded-xl border overflow-hidden"
        :class="dark ? 'border-zinc-800/70 bg-[#111113]' : 'border-zinc-200 bg-white'">
        <Link :href="route('vote.index')"
            class="px-5 py-3 border-b flex items-center gap-2 transition-colors"
            :class="dark ? 'border-zinc-800/60 hover:bg-white/[0.02]' : 'border-zinc-100 hover:bg-zinc-50'">
            <Trophy :size="14" :stroke-width="2" class="text-amber-400" />
            <span class="text-[13px] font-bold" :class="dark ? 'text-zinc-100' : 'text-zinc-900'">{{ t('ext.vote.top_voters') }}</span>
            <span class="ml-auto text-[11px]" :class="dark ? 'text-zinc-600' : 'text-zinc-400'">{{ t('ext.vote.this_month') }}</span>
        </Link>
        <div v-for="(voter, i) in voters" :key="voter.username"
            class="flex items-center gap-2.5 px-5 py-2 border-b last:border-0"
            :class="dark ? 'border-zinc-800/40' : 'border-zinc-50'">
            <span class="w-4 text-center text-[12px] font-black"
                :class="i === 0 ? 'text-amber-400' : (dark ? 'text-zinc-600' : 'text-zinc-400')">{{ i + 1 }}</span>
            <div class="w-6 h-6 rounded-md overflow-hidden shrink-0 border flex items-center justify-center"
                :class="dark ? 'border-zinc-800 bg-zinc-900' : 'border-zinc-200 bg-zinc-100'">
                <img v-if="voter.avatar" :src="voter.avatar" class="w-full h-full object-cover" />
                <span v-else class="text-[10px] font-bold" :class="dark ? 'text-zinc-500' : 'text-zinc-400'">{{ voter.username[0]?.toUpperCase() }}</span>
            </div>
            <span class="flex-1 min-w-0 text-[12px] font-medium truncate" :class="dark ? 'text-zinc-300' : 'text-zinc-700'">{{ voter.username }}</span>
            <span class="text-[11px] font-bold" :class="dark ? 'text-zinc-500' : 'text-zinc-400'">{{ voter.votes }}</span>
        </div>
    </div>
</template>
