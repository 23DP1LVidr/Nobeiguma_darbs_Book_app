<template>
  <v-col cols="12" md="3" class="d-none d-md-block">
    <AppSidebar :user="user" :initials="initials" @logout="$emit('logout')" />

    <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
      <div class="text-subtitle-2 font-weight-bold mb-3">Apmaiņu statistika</div>

      <div class="d-flex justify-space-between mb-2">
        <span class="text-body-2">Gaida</span>
        <span class="text-body-2 font-weight-bold">{{ pendingCount }}</span>
      </div>

      <div class="d-flex justify-space-between mb-2">
        <span class="text-body-2">Aktīvas</span>
        <span class="text-body-2 font-weight-bold">{{ activeCount }}</span>
      </div>

      <div class="d-flex justify-space-between">
        <span class="text-body-2">Pabeigtas</span>
        <span class="text-body-2 font-weight-bold">{{ completedCount }}</span>
      </div>
    </v-card>
  </v-col>
</template>

<script setup>
import AppSidebar from "@/components/layout/AppSidebar.vue"

defineProps({
  user: {
    type: Object,
    default: null,
  },
  initials: {
    type: String,
    required: true,
  },
  pendingCount: {
    type: Number,
    required: true,
  },
  activeCount: {
    type: Number,
    required: true,
  },
  completedCount: {
    type: Number,
    required: true,
  },
})

defineEmits(["logout"])
</script>

<style scoped>
.panel-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}
</style>
