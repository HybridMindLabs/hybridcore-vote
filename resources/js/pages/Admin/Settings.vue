<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Settings, ChevronLeft, Info } from '@lucide/vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import PageHeader from '@/components/UI/PageHeader.vue';

defineProps<{
    config: { default_cooldown_hours: number; history_retention_days: number };
    callback_example: string;
}>();
</script>

<template>
    <Head title="Voting settings" />
    <AdminLayout title="Voting settings">
        <div class="flex items-center gap-2 mb-5 text-sm">
            <Link :href="route('admin.vote.index')" class="flex items-center gap-1.5 text-zinc-500 hover:text-zinc-100 transition-colors">
                <ChevronLeft :size="13" :stroke-width="1.75" /> Voting
            </Link>
            <span class="text-zinc-700">/</span>
            <span class="text-zinc-300">Settings</span>
        </div>

        <PageHeader title="Voting settings" description="How rewards and callbacks work." :icon="Settings" />

        <div class="flex flex-col gap-4 max-w-2xl">
            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl p-5">
                <h3 class="text-zinc-100 text-sm font-semibold mb-3">Defaults</h3>
                <div class="flex flex-col gap-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-500">Default cooldown</span>
                        <span class="text-zinc-200 font-mono">{{ config.default_cooldown_hours }}h</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-500">Vote history retention</span>
                        <span class="text-zinc-200 font-mono">{{ config.history_retention_days }} days</span>
                    </div>
                </div>
                <p class="text-zinc-600 text-xs mt-3">These are set in the extension's <code class="text-zinc-400">config/vote.php</code>.</p>
            </div>

            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl p-5">
                <h3 class="text-zinc-100 text-sm font-semibold mb-2 flex items-center gap-1.5">
                    <Info :size="14" :stroke-width="1.75" class="text-blue-400" /> Vote callback (postback)
                </h3>
                <p class="text-zinc-500 text-xs leading-relaxed mb-3">
                    Configure each listing site to call your callback URL when a player votes. Each site has its
                    own URL and secret token (see <strong class="text-zinc-300">Manage sites</strong>). The player is matched by
                    <code class="text-zinc-400">steamid</code> (a linked Steam account) or <code class="text-zinc-400">username</code>.
                </p>
                <code class="block bg-zinc-900/70 border border-zinc-800 rounded-lg px-3 py-2.5 text-[12px] text-emerald-300 font-mono break-all">{{ callback_example }}</code>
            </div>

            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl p-5">
                <h3 class="text-zinc-100 text-sm font-semibold mb-2">In-game rewards</h3>
                <p class="text-zinc-500 text-xs leading-relaxed">
                    Set a reward command on a site to deliver an in-game reward through the game-server bridge on
                    every confirmed vote. It runs on all bridge-enabled servers. Placeholders:
                    <code class="text-zinc-400">{steamid}</code>, <code class="text-zinc-400">{name}</code>, <code class="text-zinc-400">{points}</code>.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
