<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Settings, ChevronLeft, Info } from '@lucide/vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import PageHeader from '@/components/UI/PageHeader.vue';
import { useLocale } from '@/composables/useLocale';

defineProps<{
    config: { default_cooldown_hours: number; history_retention_days: number };
    callback_example: string;
}>();

const { t } = useLocale();
</script>

<template>
    <Head :title="t('ext.vote.settings_title')" />
    <AdminLayout :title="t('ext.vote.settings_title')">
        <div class="flex items-center gap-2 mb-5 text-sm">
            <Link :href="route('admin.vote.index')" class="flex items-center gap-1.5 text-zinc-500 hover:text-zinc-100 transition-colors">
                <ChevronLeft :size="13" :stroke-width="1.75" /> {{ t('ext.vote.nav') }}
            </Link>
            <span class="text-zinc-700">/</span>
            <span class="text-zinc-300">{{ t('ext.vote.settings_breadcrumb') }}</span>
        </div>

        <PageHeader :title="t('ext.vote.settings_title')" :description="t('ext.vote.settings_subtitle')" :icon="Settings" />

        <div class="flex flex-col gap-4 max-w-2xl">
            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl p-5">
                <h3 class="text-zinc-100 text-sm font-semibold mb-3">{{ t('ext.vote.defaults_heading') }}</h3>
                <div class="flex flex-col gap-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-500">{{ t('ext.vote.default_cooldown_label') }}</span>
                        <span class="text-zinc-200 font-mono">{{ config.default_cooldown_hours }}h</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-500">{{ t('ext.vote.history_retention_label') }}</span>
                        <span class="text-zinc-200 font-mono">{{ config.history_retention_days }} days</span>
                    </div>
                </div>
                <p class="text-zinc-600 text-xs mt-3">{{ t('ext.vote.config_note', { path: 'config/vote.php' }) }}</p>
            </div>

            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl p-5">
                <h3 class="text-zinc-100 text-sm font-semibold mb-2 flex items-center gap-1.5">
                    <Info :size="14" :stroke-width="1.75" class="text-blue-400" /> {{ t('ext.vote.callback_heading') }}
                </h3>
                <p class="text-zinc-500 text-xs leading-relaxed mb-3">
                    {{ t('ext.vote.callback_body', { steamid: 'steamid', username: 'username' }) }}
                </p>
                <code class="block bg-zinc-900/70 border border-zinc-800 rounded-lg px-3 py-2.5 text-[12px] text-emerald-300 font-mono break-all">{{ callback_example }}</code>
            </div>

            <div class="bg-[#111113] border border-zinc-800/70 rounded-xl p-5">
                <h3 class="text-zinc-100 text-sm font-semibold mb-2">{{ t('ext.vote.rewards_heading') }}</h3>
                <p class="text-zinc-500 text-xs leading-relaxed">
                    {{ t('ext.vote.rewards_body') }}
                    {{ t('ext.vote.field_placeholders') }}
                    <code class="text-zinc-400">{steamid}</code>, <code class="text-zinc-400">{name}</code>, <code class="text-zinc-400">{points}</code>.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
