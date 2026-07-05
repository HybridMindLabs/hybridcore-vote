<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ThumbsUp, Trophy, Settings, ListChecks, CheckCircle2, Clock } from '@lucide/vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import PageHeader from '@/components/UI/PageHeader.vue';

interface RecentVote { id: number; user: string; site: string | null; points: number; delivered: boolean; at: string }
interface TopVoter { username: string; votes: number; points: number }

defineProps<{
    stats: { sites: number; active_sites: number; votes_today: number; votes_month: number };
    recentVotes: RecentVote[];
    topVoters: TopVoter[];
}>();
</script>

<template>
    <Head title="Voting" />
    <AdminLayout title="Voting">
        <PageHeader title="Voting" description="Vote sites, rewards and the top-voters leaderboard." :icon="ThumbsUp">
            <template #actions>
                <Link :href="route('admin.vote.sites.index')"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-blue-500/30 bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors">
                    <ListChecks :size="12" :stroke-width="1.75" /> Manage sites
                </Link>
                <Link :href="route('admin.vote.settings')"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-zinc-700 text-zinc-400 hover:text-zinc-100 hover:border-zinc-600 transition-colors">
                    <Settings :size="12" :stroke-width="1.75" /> Settings
                </Link>
            </template>
        </PageHeader>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
            <div v-for="tile in [
                { label: 'Vote sites', value: stats.sites, icon: ListChecks, color: 'text-blue-400' },
                { label: 'Active sites', value: stats.active_sites, icon: CheckCircle2, color: 'text-emerald-400' },
                { label: 'Votes today', value: stats.votes_today, icon: ThumbsUp, color: 'text-violet-400' },
                { label: 'Votes this month', value: stats.votes_month, icon: Trophy, color: 'text-amber-400' },
            ]" :key="tile.label" class="bg-[#111113] border border-zinc-800/70 rounded-xl px-4 py-3 flex flex-col gap-1">
                <component :is="tile.icon" :size="14" :stroke-width="1.75" :class="tile.color" />
                <span class="text-zinc-100 text-xl font-black leading-none">{{ tile.value }}</span>
                <span class="text-zinc-600 text-[11px]">{{ tile.label }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_300px] gap-5 items-start">
            <!-- Recent votes -->
            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-zinc-800/60 bg-[#1a1a1e]">
                    <p class="text-[13px] font-bold text-zinc-100">Recent votes</p>
                </div>
                <div v-if="!recentVotes.length" class="px-5 py-10 text-center text-[13px] text-zinc-600">No votes yet.</div>
                <div v-for="v in recentVotes" :key="v.id"
                    class="flex items-center gap-3 px-5 py-2.5 border-b border-zinc-800/40 last:border-0">
                    <span class="text-[13px] font-semibold text-zinc-200">{{ v.user }}</span>
                    <span class="text-[12px] text-zinc-600">voted on</span>
                    <span class="text-[12px] text-zinc-400">{{ v.site ?? '—' }}</span>
                    <span v-if="v.points" class="text-[11px] font-bold text-amber-400">+{{ v.points }}</span>
                    <component :is="v.delivered ? CheckCircle2 : Clock" :size="12" :stroke-width="2"
                        :class="v.delivered ? 'text-emerald-500' : 'text-zinc-600'"
                        :title="v.delivered ? 'Reward delivered' : 'Reward pending'" />
                    <span class="ml-auto text-[11px] text-zinc-600">{{ v.at }}</span>
                </div>
            </div>

            <!-- Top voters -->
            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-zinc-800/60 bg-[#1a1a1e] flex items-center gap-2">
                    <Trophy :size="14" :stroke-width="2" class="text-amber-400" />
                    <p class="text-[13px] font-bold text-zinc-100">Top voters this month</p>
                </div>
                <div v-if="!topVoters.length" class="px-5 py-8 text-center text-[12px] text-zinc-600">—</div>
                <div v-for="(t, i) in topVoters" :key="t.username"
                    class="flex items-center gap-3 px-5 py-2.5 border-b border-zinc-800/40 last:border-0">
                    <span class="w-4 text-center text-[13px] font-black"
                        :class="i === 0 ? 'text-amber-400' : 'text-zinc-600'">{{ i + 1 }}</span>
                    <span class="flex-1 min-w-0 text-[13px] font-semibold text-zinc-200 truncate">{{ t.username }}</span>
                    <span class="text-[12px] font-bold text-zinc-500">{{ t.votes }}</span>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
