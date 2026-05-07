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

    <template v-else>
      <v-menu location="bottom end" offset="10" transition="scale-transition">
        <template #activator="{ props }">
          <v-btn icon size="small" variant="text" class="ml-1 mr-1" v-bind="props">
            <v-icon>mdi-menu-down</v-icon>
          </v-btn>
        </template>

        <v-card rounded="lg" elevation="8" class="profile-menu">
          <div class="d-flex align-center pa-4">
            <v-avatar size="48" color="primary" class="mr-3">
              <v-img v-if="user?.avatar" :src="user.avatar" />
              <span v-else class="text-white">{{ initials }}</span>
            </v-avatar>

            <div>
              <div class="text-subtitle-1 font-weight-bold">
                {{ user?.name }} {{ user?.surname }}
              </div>
              <div class="text-caption text-medium-emphasis">
                @{{ user?.username }}
              </div>
            </div>
          </div>

          <v-divider />

          <v-list density="compact" nav bg-color="transparent">
            <v-list-item prepend-icon="mdi-account" title="Profils" to="/profile" />
            <v-list-item prepend-icon="mdi-home" title="Sākums" to="/main" />
            <v-list-item prepend-icon="mdi-book-open-variant" title="Mana bibliotēka" to="/library" />
            <v-list-item prepend-icon="mdi-swap-horizontal" title="Manas apmaiņas" to="/exchanges" />
            <v-list-item prepend-icon="mdi-message-text" title="Ziņas" to="/messages" />
            <v-list-item prepend-icon="mdi-cog" title="Iestatījumi" to="/settings" />
            <v-list-item
              v-if="user?.role === 'admin'"
              prepend-icon="mdi-shield-account"
              title="Admin"
              to="/admin"
            />
          </v-list>
        </v-card>
      </v-menu>

      <router-link to="/profile" class="text-decoration-none">
        <v-avatar size="36" color="primary" class="header-avatar">
          <v-img v-if="user?.avatar" :src="user.avatar" />
          <span v-else class="text-white text-caption">{{ initials }}</span>
        </v-avatar>
      </router-link>
    </template>
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

.header-avatar {
  cursor: pointer;
}

.profile-menu {
  width: 280px;
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}
</style>
