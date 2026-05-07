<template>
  <v-card rounded="xl" elevation="0" class="pa-4 content-card">
    <div class="text-subtitle-1 font-weight-bold mb-4">Sarunas</div>

    <v-text-field
      :model-value="search"
      placeholder="Meklēt..."
      prepend-inner-icon="mdi-magnify"
      variant="outlined"
      density="compact"
      hide-details
      class="mb-4 search-field"
      @update:model-value="$emit('update:search', $event)"
    />

    <div v-if="loading" class="text-center py-6">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <v-list v-else bg-color="transparent">
      <v-list-item
        v-for="chat in chats"
        :key="chat.id"
        :active="selectedChat?.id === chat.id"
        rounded="xl"
        :class="['mb-2 chat-item', { 'chat-item--new': chat.hasNewMessage }]"
        @click="$emit('select', chat)"
      >
        <template #prepend>
          <v-avatar color="primary">
            <v-img v-if="chat.otherUser?.avatar" :src="chat.otherUser.avatar" />
            <span v-else class="text-white">{{ userInitials(chat.otherUser) }}</span>
          </v-avatar>
        </template>

        <v-list-item-title class="font-weight-bold">
          {{ chat.otherUser?.name }} {{ chat.otherUser?.surname }}
        </v-list-item-title>

        <v-list-item-subtitle>
          {{ chat.lastMessage || "Nav ziņu" }}
        </v-list-item-subtitle>

        <template #append>
          <v-icon v-if="chat.hasNewMessage" color="error" size="18">
            mdi-circle
          </v-icon>
        </template>
      </v-list-item>

      <div v-if="chats.length === 0" class="text-center text-medium-emphasis py-6">
        Nav aktīvu sarunu.
      </div>
    </v-list>
  </v-card>
</template>

<script setup>
defineProps({
  chats: {
    type: Array,
    default: () => [],
  },
  selectedChat: {
    type: Object,
    default: null,
  },
  search: {
    type: String,
    default: "",
  },
  loading: {
    type: Boolean,
    default: false,
  },
  userInitials: {
    type: Function,
    required: true,
  },
})

defineEmits(["update:search", "select"])
</script>

<style scoped>
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.search-field :deep(.v-field) {
  background: rgba(var(--v-theme-on-surface), 0.05);
}

.chat-item {
  border: 1px solid transparent;
}

.chat-item--new {
  background: rgba(var(--v-theme-primary), 0.12);
  border-color: rgba(var(--v-theme-primary), 0.32);
}

.chat-item--new :deep(.v-list-item-subtitle) {
  color: rgb(var(--v-theme-on-surface));
  font-weight: 600;
}
</style>
