<template>
  <v-app-bar elevation="1" class="px-4 app-header">
    <div class="d-flex align-center" style="width: 280px;">
      <router-link to="/main" class="text-decoration-none">
        <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
      </router-link>
    </div>

    <v-spacer />

    <router-link to="/profile" class="text-decoration-none">
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
