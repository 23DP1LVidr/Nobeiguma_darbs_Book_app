<template>
  <v-app-bar elevation="1" class="px-4 app-header">
    <div class="d-flex align-center" style="width: 280px;">
      <router-link to="/main" class="text-decoration-none">
        <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
      </router-link>
    </div>

    <v-spacer />

    <v-btn
      v-if="user?.role === 'admin'"
      to="/admin"
      variant="tonal"
      size="small"
      prepend-icon="mdi-shield-account"
      class="mr-2"
    >
      Admin
    </v-btn>

    <div v-if="!user" class="d-flex align-center gap-2">
      <v-btn to="/login" variant="text" size="small">
        Pieteikties
      </v-btn>

      <v-btn to="/register" color="primary" variant="tonal" size="small">
        Reģistrēties
      </v-btn>
    </div>

    <router-link v-else to="/profile" class="text-decoration-none">
      <v-avatar size="36" color="primary" class="ml-2" style="cursor:pointer;">
        <v-img v-if="user?.avatar" :src="user.avatar" />
        <span v-else class="text-white text-caption">{{ initials }}</span>
      </v-avatar>
    </router-link>
  </v-app-bar>
</template>

<script setup>
defineProps({
  user: {
    type: Object,
    default: null,
  },
  initials: {
    type: String,
    default: "U",
  },
})
</script>

<style scoped>
.app-header {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.gap-2 {
  gap: 8px;
}
</style>
