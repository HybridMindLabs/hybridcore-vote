<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ThumbsUp, Plus, Pencil, Trash2, KeyRound, Copy, Check, ChevronLeft, X } from '@lucide/vue';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useLocale } from '@/composables/useLocale';

interface Site {
    id: number; name: string; slug: string; vote_url: string; icon: string | null;
    reward_points: number; cooldown_hours: number; is_active: boolean;
    votes_count: number; has_command: boolean; callback_url: string; has_token: boolean;
}

const props = defineProps<{ sites: Site[] }>();

const { t } = useLocale();

const editing = ref<Site | null>(null);
const showForm = ref(false);
const copied = ref<number | null>(null);

const form = useForm({
    name: '', vote_url: '', icon: '', reward_points: 10,
    reward_command: '', cooldown_hours: 12, is_active: true, sort: 0,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showForm.value = true;
}

function openEdit(site: Site) {
    editing.value = site;
    form.clearErrors();
    form.name = site.name;
    form.vote_url = site.vote_url;
    form.icon = site.icon ?? '';
    form.reward_points = site.reward_points;
    form.cooldown_hours = site.cooldown_hours;
    form.is_active = site.is_active;
    showForm.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.vote.sites.update', editing.value.id), { onSuccess: () => (showForm.value = false) });
    } else {
        form.post(route('admin.vote.sites.store'), { onSuccess: () => (showForm.value = false) });
    }
}

function destroy(site: Site) {
    if (!confirm(t('ext.vote.confirm_delete_site', { name: site.name }))) return;
    router.delete(route('admin.vote.sites.destroy', site.id), { preserveScroll: true });
}

function rotateToken(site: Site) {
    if (!confirm(t('ext.vote.confirm_rotate_token', { name: site.name }))) return;
    router.post(route('admin.vote.sites.rotate-token', site.id), {}, { preserveScroll: true });
}

async function copyCallback(site: Site) {
    await navigator.clipboard.writeText(site.callback_url);
    copied.value = site.id;
    setTimeout(() => (copied.value = null), 2000);
}

const input = 'w-full bg-zinc-900/60 border border-zinc-800/70 text-zinc-100 rounded-lg px-3 py-2 text-sm placeholder:text-zinc-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20';
const label = 'text-zinc-400 text-[11px] font-bold uppercase tracking-wider mb-1.5 block';
</script>

<template>
    <Head :title="t('ext.vote.sites_breadcrumb')" />
    <AdminLayout :title="t('ext.vote.sites_breadcrumb')">
        <div class="flex items-center gap-2 mb-5 text-sm">
            <Link :href="route('admin.vote.index')" class="flex items-center gap-1.5 text-zinc-500 hover:text-zinc-100 transition-colors">
                <ChevronLeft :size="13" :stroke-width="1.75" /> {{ t('ext.vote.nav') }}
            </Link>
            <span class="text-zinc-700">/</span>
            <span class="text-zinc-300">{{ t('ext.vote.sites_breadcrumb') }}</span>
            <button type="button" class="ml-auto flex items-center gap-1.5 bg-blue-500 text-white font-semibold rounded-lg px-3 py-1.5 text-xs hover:bg-blue-400 transition-colors" @click="openCreate">
                <Plus :size="12" :stroke-width="2" /> {{ t('ext.vote.add_site') }}
            </button>
        </div>

        <div class="bg-[#111113] border border-zinc-800/70 rounded-xl overflow-hidden">
            <div v-if="!sites.length" class="px-5 py-12 text-center">
                <ThumbsUp :size="24" :stroke-width="1.5" class="mx-auto text-zinc-700 mb-2" />
                <p class="text-[13px] text-zinc-600">{{ t('ext.vote.no_sites_admin') }}</p>
            </div>

            <div v-for="site in sites" :key="site.id"
                class="px-5 py-3.5 border-b border-zinc-800/50 last:border-0 flex items-center gap-4">
                <div class="w-9 h-9 rounded-lg shrink-0 overflow-hidden border border-zinc-800 bg-zinc-900 flex items-center justify-center">
                    <img v-if="site.icon" :src="site.icon" class="w-full h-full object-cover" />
                    <ThumbsUp v-else :size="15" :stroke-width="1.8" class="text-zinc-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-[14px] font-bold text-zinc-100 truncate">{{ site.name }}</p>
                        <span v-if="!site.is_active" class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-zinc-800 text-zinc-500">{{ t('ext.vote.off') }}</span>
                        <span v-if="site.has_command" class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400">{{ t('ext.vote.reward_badge') }}</span>
                    </div>
                    <button type="button" class="text-[11px] text-zinc-600 font-mono truncate hover:text-blue-400 transition-colors flex items-center gap-1" @click="copyCallback(site)">
                        <component :is="copied === site.id ? Check : Copy" :size="10" :stroke-width="2" />
                        {{ site.callback_url }}
                    </button>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[13px] font-bold text-amber-400">{{ t('ext.vote.sites_pts', { points: site.reward_points }) }}</p>
                    <p class="text-[11px] text-zinc-600">{{ t('ext.vote.sites_votes_cooldown', { votes: site.votes_count, hours: site.cooldown_hours }) }}</p>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" :title="t('ext.vote.rotate_token_title')" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-500 hover:text-amber-400 hover:bg-amber-500/10 transition-colors" @click="rotateToken(site)">
                        <KeyRound :size="13" :stroke-width="1.75" />
                    </button>
                    <button type="button" :title="t('ext.vote.edit')" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 transition-colors" @click="openEdit(site)">
                        <Pencil :size="13" :stroke-width="1.75" />
                    </button>
                    <button type="button" :title="t('ext.vote.delete')" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-500 hover:text-red-400 hover:bg-red-500/10 transition-colors" @click="destroy(site)">
                        <Trash2 :size="13" :stroke-width="1.75" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Create / edit modal -->
        <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4" @mousedown.self="showForm = false">
            <div class="w-full max-w-lg bg-[#111113] border border-zinc-800 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-zinc-100 text-base font-bold">{{ editing ? t('ext.vote.edit_site_title') : t('ext.vote.add_site_title') }}</h3>
                    <button type="button" class="text-zinc-600 hover:text-zinc-300" @click="showForm = false"><X :size="16" /></button>
                </div>
                <form class="flex flex-col gap-3" @submit.prevent="submit">
                    <div>
                        <label :class="label">{{ t('ext.vote.field_name') }}</label>
                        <input v-model="form.name" type="text" :class="input" placeholder="TopG" />
                        <p v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label :class="label">{{ t('ext.vote.field_vote_url') }}</label>
                        <input v-model="form.vote_url" type="url" :class="input" placeholder="https://topg.org/..." />
                        <p v-if="form.errors.vote_url" class="text-red-400 text-xs mt-1">{{ form.errors.vote_url }}</p>
                    </div>
                    <div>
                        <label :class="label">{{ t('ext.vote.field_icon') }} <span class="text-zinc-600 normal-case font-normal">{{ t('ext.vote.field_icon_optional') }}</span></label>
                        <input v-model="form.icon" type="url" :class="input" placeholder="https://.../logo.png" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label :class="label">{{ t('ext.vote.field_reward_points') }}</label>
                            <input v-model.number="form.reward_points" type="number" min="0" :class="input" />
                        </div>
                        <div>
                            <label :class="label">{{ t('ext.vote.field_cooldown_hours') }}</label>
                            <input v-model.number="form.cooldown_hours" type="number" min="1" max="168" :class="input" />
                        </div>
                    </div>
                    <div>
                        <label :class="label">{{ t('ext.vote.field_reward_command') }} <span class="text-zinc-600 normal-case font-normal">{{ t('ext.vote.field_reward_command_optional') }}</span></label>
                        <input v-model="form.reward_command" type="text" :class="[input, 'font-mono text-[12px]']" placeholder="hc_give_points {steamid} {points}" />
                        <p class="text-zinc-600 text-[11px] mt-1">{{ t('ext.vote.field_placeholders') }} <code class="text-zinc-400">{steamid}</code> <code class="text-zinc-400">{name}</code> <code class="text-zinc-400">{points}</code></p>
                        <p v-if="form.errors.reward_command" class="text-red-400 text-xs mt-1">{{ form.errors.reward_command }}</p>
                    </div>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 accent-blue-500" />
                        <span class="text-zinc-300 text-sm">{{ t('ext.vote.field_active_hint') }}</span>
                    </label>
                    <button type="submit" :disabled="form.processing" class="mt-2 bg-blue-500 text-white font-semibold rounded-lg px-4 py-2 text-sm hover:bg-blue-400 transition-colors disabled:opacity-50">
                        {{ form.processing ? t('ext.vote.saving') : (editing ? t('ext.vote.save_changes') : t('ext.vote.add_site')) }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
