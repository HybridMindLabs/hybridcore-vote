<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ThumbsUp, Award } from '@lucide/vue';
import { computed } from 'vue';
import { useTheme } from '@/composables/useTheme';
import { useLocale } from '@/composables/useLocale';
import AccountPage from '@/components/Account/AccountPage.vue';

interface VoteRow { id: number; site: string; points: number; at: string | null }

defineProps<{
    points: number;
    count: number;
    recent: VoteRow[];
    unreadNotifications?: number;
    unreadMessages?: number;
}>();

const { theme } = useTheme();
const { t } = useLocale();
const dark = computed(() => theme.value === 'dark');

function when(iso: string | null): string {
    if (!iso) return '';
    return new Date(iso).toLocaleString();
}
</script>

<template>
    <Head :title="t('ext.vote.account_tab')" />

    <AccountPage active-tab="" :section="t('ext.vote.account_tab')" :unread-notifications="unreadNotifications" :unread-messages="unreadMessages">
        <template #subtitle>{{ t('ext.vote.account_subtitle') }}</template>

        <div class="flex gap-3 mb-4">
            <div class="flex-1 rounded-2xl border px-5 py-4" :class="dark ? 'border-zinc-800/70 bg-[#111113]' : 'border-zinc-200 bg-white shadow-sm'">
                <p class="text-2xl font-black" :class="dark ? 'text-zinc-100' : 'text-zinc-900'">{{ points }}</p>
                <p class="text-[11px] uppercase tracking-wide" :class="dark ? 'text-zinc-500' : 'text-zinc-400'">{{ t('ext.vote.your_points') }}</p>
            </div>
            <div class="flex-1 rounded-2xl border px-5 py-4" :class="dark ? 'border-zinc-800/70 bg-[#111113]' : 'border-zinc-200 bg-white shadow-sm'">
                <p class="text-2xl font-black" :class="dark ? 'text-zinc-100' : 'text-zinc-900'">{{ count }}</p>
                <p class="text-[11px] uppercase tracking-wide" :class="dark ? 'text-zinc-500' : 'text-zinc-400'">{{ t('ext.vote.your_votes') }}</p>
            </div>
        </div>

        <div class="rounded-2xl border overflow-hidden" :class="dark ? 'border-zinc-800/70 bg-[#111113]' : 'border-zinc-200 bg-white shadow-sm'">
            <div class="px-6 py-4 border-b" :class="dark ? 'border-zinc-800/60 bg-[#1a1a1e]' : 'border-zinc-100 bg-zinc-50'">
                <p class="text-[14px] font-black" :class="dark ? 'text-zinc-100' : 'text-zinc-900'">{{ t('ext.vote.history') }}</p>
            </div>

            <div v-if="recent.length === 0" class="p-12 text-center">
                <ThumbsUp :size="28" :stroke-width="1.5" class="mx-auto mb-3" :class="dark ? 'text-zinc-700' : 'text-zinc-300'" />
                <p class="text-[13px] font-semibold" :class="dark ? 'text-zinc-500' : 'text-zinc-400'">{{ t('ext.vote.no_history') }}</p>
            </div>

            <div v-else class="divide-y" :class="dark ? 'divide-zinc-800/60' : 'divide-zinc-100'">
                <div v-for="row in recent" :key="row.id" class="flex items-center gap-4 px-5 py-3">
                    <div class="w-8 h-8 rounded-lg shrink-0 border flex items-center justify-center"
                        :class="dark ? 'border-zinc-800 bg-zinc-900' : 'border-zinc-200 bg-zinc-100'">
                        <ThumbsUp :size="14" :stroke-width="1.8" :class="dark ? 'text-zinc-500' : 'text-zinc-400'" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-bold truncate" :class="dark ? 'text-zinc-100' : 'text-zinc-800'">{{ row.site }}</p>
                        <p class="text-[11px]" :class="dark ? 'text-zinc-600' : 'text-zinc-400'">{{ when(row.at) }}</p>
                    </div>
                    <span v-if="row.points" class="flex items-center gap-1 text-[12px] font-bold text-amber-400 shrink-0">
                        <Award :size="12" :stroke-width="2" /> +{{ row.points }}
                    </span>
                </div>
            </div>
        </div>
    </AccountPage>
</template>
