<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ThumbsUp, Trophy, Clock, ExternalLink, Award } from '@lucide/vue';
import { computed, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Breadcrumb from '@/components/UI/Breadcrumb.vue';
import { useTheme } from '@/composables/useTheme';
import { useLocale } from '@/composables/useLocale';

interface Site {
    id: number; name: string; slug: string; vote_url: string;
    icon: string | null; reward_points: number;
    can_vote: boolean | null; next_vote_at: string | null;
}
interface Voter { username: string; avatar: string | null; votes: number; points: number }

defineProps<{
    sites: Site[];
    topVoters: { month: Voter[]; all: Voter[] };
    me: { points: number; votes: number } | null;
}>();

const { theme } = useTheme();
const { t } = useLocale();
const dark = computed(() => theme.value === 'dark');
const page = usePage();
const authed = computed(() => !!(page.props.auth as { user?: unknown } | null)?.user);

const period = ref<'month' | 'all'>('month');

function cooldownLabel(iso: string | null): string {
    if (!iso) return '';
    const diff = new Date(iso).getTime() - Date.now();
    if (diff <= 0) return '';
    const h = Math.floor(diff / 3_600_000);
    const m = Math.floor((diff % 3_600_000) / 60_000);
    return h > 0 ? `${h}h ${m}m` : `${m}m`;
}

const card = computed(() => dark.value ? 'border-zinc-800/70 bg-[#111113]' : 'border-zinc-200 bg-white');
const textPri = computed(() => dark.value ? 'text-zinc-100' : 'text-zinc-900');
const textMute = computed(() => dark.value ? 'text-zinc-500' : 'text-zinc-400');
</script>

<template>
    <Head :title="t('ext.vote.title')" />
    <PublicLayout>
        <div class="border-b" :class="dark ? 'border-zinc-800/60 bg-[#09090b]' : 'border-zinc-200 bg-zinc-50'">
            <div class="relative max-w-[1600px] mx-auto px-4 sm:px-6 py-14">
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-40 left-1/3 w-[600px] h-[400px] rounded-full blur-[130px]"
                        :class="dark ? 'bg-blue-500/6' : 'bg-blue-400/8'" />
                </div>
                <div class="relative z-10">
                    <Breadcrumb :items="[{ label: 'Home', href: '/' }, { label: t('ext.vote.title') }]" />
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-[11px] font-bold uppercase tracking-widest mt-4 mb-5"
                        :class="dark ? 'border-blue-500/25 bg-blue-500/8 text-blue-400' : 'border-blue-400/30 bg-blue-50 text-blue-600'">
                        <ThumbsUp :size="11" :stroke-width="2.2" /> {{ t('ext.vote.nav') }}
                    </div>
                    <h1 class="text-4xl font-black tracking-tight" :class="textPri">{{ t('ext.vote.title') }}</h1>
                    <p class="mt-3 text-[15px] max-w-lg" :class="textMute">{{ t('ext.vote.subtitle') }}</p>
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 py-8 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-6 items-start">

            <!-- Vote sites -->
            <div class="flex flex-col gap-3">
                <div v-if="me" class="flex gap-3">
                    <div class="flex-1 rounded-xl border px-4 py-3" :class="card">
                        <p class="text-2xl font-black" :class="textPri">{{ me.points }}</p>
                        <p class="text-[11px] uppercase tracking-wide" :class="textMute">{{ t('ext.vote.your_points') }}</p>
                    </div>
                    <div class="flex-1 rounded-xl border px-4 py-3" :class="card">
                        <p class="text-2xl font-black" :class="textPri">{{ me.votes }}</p>
                        <p class="text-[11px] uppercase tracking-wide" :class="textMute">{{ t('ext.vote.your_votes') }}</p>
                    </div>
                </div>

                <div v-if="!sites.length" class="rounded-xl border px-5 py-10 text-center" :class="card">
                    <ThumbsUp :size="24" :stroke-width="1.5" class="mx-auto mb-2" :class="textMute" />
                    <p class="text-[13px]" :class="textMute">{{ t('ext.vote.no_sites') }}</p>
                </div>

                <div v-for="site in sites" :key="site.id"
                    class="rounded-xl border px-4 py-3.5 flex items-center gap-4" :class="card">
                    <div class="w-10 h-10 rounded-lg shrink-0 overflow-hidden border flex items-center justify-center"
                        :class="dark ? 'border-zinc-800 bg-zinc-900' : 'border-zinc-200 bg-zinc-100'">
                        <img v-if="site.icon" :src="site.icon" :alt="site.name" class="w-full h-full object-cover" />
                        <ThumbsUp v-else :size="16" :stroke-width="1.8" :class="textMute" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[14px] font-bold truncate" :class="textPri">{{ site.name }}</p>
                        <p v-if="site.reward_points" class="text-[12px] flex items-center gap-1 text-amber-400">
                            <Award :size="12" :stroke-width="2" /> {{ t('ext.vote.reward', { points: site.reward_points }) }}
                        </p>
                    </div>
                    <a v-if="site.can_vote !== false" :href="site.vote_url" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[13px] font-bold bg-blue-500 text-white hover:bg-blue-400 transition-colors shrink-0">
                        <ExternalLink :size="13" :stroke-width="2" /> {{ t('ext.vote.vote_now') }}
                    </a>
                    <span v-else class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[13px] font-semibold shrink-0"
                        :class="dark ? 'bg-zinc-800 text-zinc-500' : 'bg-zinc-100 text-zinc-400'">
                        <Clock :size="13" :stroke-width="2" /> {{ cooldownLabel(site.next_vote_at) }}
                    </span>
                </div>

                <p v-if="!authed && sites.length" class="text-[12px] text-center mt-2" :class="textMute">
                    {{ t('ext.vote.login_to_earn') }}
                    <Link :href="route('login')" class="text-blue-400 font-semibold hover:underline">{{ t('navigation.login') ?? 'Login' }}</Link>
                </p>
            </div>

            <!-- Top voters -->
            <div class="rounded-xl border overflow-hidden" :class="card">
                <div class="px-5 py-3.5 border-b flex items-center justify-between" :class="dark ? 'border-zinc-800/60' : 'border-zinc-100'">
                    <p class="text-[13px] font-bold flex items-center gap-2" :class="textPri">
                        <Trophy :size="14" :stroke-width="2" class="text-amber-400" /> {{ t('ext.vote.top_voters') }}
                    </p>
                    <div class="flex gap-1">
                        <button v-for="p in (['month', 'all'] as const)" :key="p" type="button"
                            class="px-2 py-1 rounded text-[11px] font-semibold transition-colors"
                            :class="period === p ? 'bg-blue-500/15 text-blue-400' : (dark ? 'text-zinc-500 hover:text-zinc-300' : 'text-zinc-400 hover:text-zinc-600')"
                            @click="period = p">{{ p === 'month' ? t('ext.vote.this_month') : t('ext.vote.all_time') }}</button>
                    </div>
                </div>
                <div v-if="!topVoters[period].length" class="px-5 py-8 text-center text-[12px]" :class="textMute">—</div>
                <div v-for="(voter, i) in topVoters[period]" :key="voter.username"
                    class="flex items-center gap-3 px-5 py-2.5 border-b last:border-0" :class="dark ? 'border-zinc-800/40' : 'border-zinc-50'">
                    <span class="w-5 text-center text-[13px] font-black"
                        :class="i === 0 ? 'text-amber-400' : i === 1 ? 'text-zinc-400' : i === 2 ? 'text-orange-600' : textMute">{{ i + 1 }}</span>
                    <div class="w-7 h-7 rounded-lg overflow-hidden shrink-0 border flex items-center justify-center"
                        :class="dark ? 'border-zinc-800 bg-zinc-900' : 'border-zinc-200 bg-zinc-100'">
                        <img v-if="voter.avatar" :src="voter.avatar" class="w-full h-full object-cover" />
                        <span v-else class="text-[11px] font-bold" :class="textMute">{{ voter.username[0]?.toUpperCase() }}</span>
                    </div>
                    <span class="flex-1 min-w-0 text-[13px] font-semibold truncate" :class="textPri">{{ voter.username }}</span>
                    <span class="text-[12px] font-bold" :class="textMute">{{ voter.votes }}</span>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
