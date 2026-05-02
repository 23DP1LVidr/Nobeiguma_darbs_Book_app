<template>
  <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
    <div class="d-flex align-center mb-4">
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

    <v-divider class="mb-3" />

    <v-list density="compact" bg-color="transparent" nav>
      <v-list-item prepend-icon="mdi-home" title="Sākums" to="/main" />
      <v-list-item prepend-icon="mdi-book-open-variant" title="Mana bibliotēka" to="/library" />
      <v-list-item prepend-icon="mdi-swap-horizontal" title="Manas apmaiņas" to="/exchanges" />
      <v-list-item prepend-icon="mdi-message-text" title="Ziņas" to="/messages" />
      <v-list-item prepend-icon="mdi-cog" title="Iestatījumi" to="/settings" />
      <v-list-item prepend-icon="mdi-logout" title="Izrakstīties" @click="$emit('logout')" />
    </v-list>
  </v-card>
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
