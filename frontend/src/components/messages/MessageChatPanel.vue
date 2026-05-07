<template>
  <v-card rounded="xl" elevation="0" class="chat-card content-card">
    <div v-if="selectedChat" class="chat-header">
      <div class="d-flex align-center">
        <v-avatar color="primary" class="mr-3">
          <v-img v-if="selectedChat.otherUser?.avatar" :src="selectedChat.otherUser.avatar" />
          <span v-else class="text-white">{{ userInitials(selectedChat.otherUser) }}</span>
        </v-avatar>

        <div>
          <div class="text-subtitle-1 font-weight-bold">
            {{ selectedChat.otherUser?.name }} {{ selectedChat.otherUser?.surname }}
          </div>

          <div class="text-caption text-medium-emphasis">
            {{ selectedChat.otherUser?.city || "Pilsēta nav norādīta" }}
          </div>
        </div>
      </div>
    </div>

    <div v-else class="chat-header">
      <div class="text-subtitle-1 font-weight-bold">Izvēlieties sarunu</div>
    </div>

    <v-divider />

    <div class="messages-area">
      <div v-if="loading" class="text-center py-8">
        <v-progress-circular indeterminate color="primary" />
      </div>

      <template v-else>
        <div
          v-for="message in messages"
          :key="message.id"
        >
          <div v-if="message.user_id === null" class="message-row">
            <div class="system-message">
              {{ message.message }}
            </div>
          </div>

          <div
            v-else
            class="message-row"
            :class="{ mine: Number(message.user_id) === Number(currentUserId) }"
          >
            <div class="message-bubble" :class="{ mine: Number(message.user_id) === Number(currentUserId) }">
              {{ message.message }}
            </div>
          </div>
        </div>
      </template>
    </div>

    <v-divider />

    <div class="message-input">
      <v-text-field
        :model-value="newMessage"
        placeholder="Rakstīt ziņu..."
        variant="outlined"
        density="compact"
        hide-details
        class="search-field"
        :disabled="!selectedChat"
        @update:model-value="$emit('update:newMessage', $event)"
        @keyup.enter="$emit('send')"
      />

      <v-btn icon color="primary" class="ml-2" :disabled="!selectedChat" @click="$emit('send')">
        <v-icon>mdi-send</v-icon>
      </v-btn>
    </div>
  </v-card>
</template>

<script setup>
defineProps({
  selectedChat: {
    type: Object,
    default: null,
  },
  messages: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  newMessage: {
    type: String,
    default: "",
  },
  currentUserId: {
    type: [Number, String],
    default: null,
  },
  userInitials: {
    type: Function,
    required: true,
  },
})

defineEmits(["update:newMessage", "send"])
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

.chat-card {
  min-height: 650px;
  display: flex;
  flex-direction: column;
}

.chat-header {
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.messages-area {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
}

.message-row {
  display: flex;
  margin-bottom: 10px;
}

.message-row.mine {
  justify-content: flex-end;
}

.message-bubble {
  max-width: 75%;
  padding: 10px 14px;
  border-radius: 18px;
  background: rgba(var(--v-theme-on-surface), 0.08);
}

.message-bubble.mine {
  background: rgb(var(--v-theme-primary));
  color: white;
}

.message-input {
  padding: 16px;
  display: flex;
  align-items: center;
}

.system-message {
  max-width: 70%;
  margin: 0 auto 12px auto;
  padding: 12px 16px;
  border-radius: 14px;
  text-align: center;
  background: rgba(var(--v-theme-primary), 0.12);
  border: 1px solid rgba(var(--v-theme-primary), 0.25);
}
</style>
