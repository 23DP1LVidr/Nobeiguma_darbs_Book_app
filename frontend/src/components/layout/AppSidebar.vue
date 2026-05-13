<template>
  <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
    <router-link to="/profile" class="sidebar-profile-link d-flex align-center mb-4">
      <v-avatar size="48" color="primary" class="mr-3">
        <v-img v-if="user?.avatar" :src="user.avatar" />
        <span v-else class="text-white">{{ initials }}</span>
      </v-avatar>

      <div>
        <div class="text-subtitle-1 font-weight-bold">
          {{ user ? `${user.name} ${user.surname}` : "Viesis" }}
        </div>
        <div class="text-caption text-medium-emphasis">
          {{ user ? `@${user.username}` : "Pārlūko publisko saturu" }}
        </div>
      </div>
    </router-link>

    <v-divider class="mb-3" />

    <v-list density="compact" bg-color="transparent" nav>
      <v-list-item prepend-icon="mdi-home" title="Sākums" to="/main" />
      <v-list-item prepend-icon="mdi-book-open-variant" title="Mana bibliotēka" to="/library" />
      <v-list-item prepend-icon="mdi-swap-horizontal" title="Manas apmaiņas" to="/exchanges">
        <template #append>
          <v-chip
            v-if="newExchangeRequestsCount"
            color="error"
            size="x-small"
            variant="flat"
          >
            {{ newExchangeRequestsCount }}
          </v-chip>
        </template>
      </v-list-item>
      <v-list-item prepend-icon="mdi-message-text" title="Ziņas" to="/messages">
        <template #append>
          <v-chip
            v-if="newMessagesCount"
            color="error"
            size="x-small"
            variant="flat"
          >
            {{ newMessagesCount }}
          </v-chip>
        </template>
      </v-list-item>
      <v-list-item prepend-icon="mdi-cog" title="Iestatījumi" to="/settings" />
      <v-list-item
        v-if="user?.role === 'admin'"
        prepend-icon="mdi-shield-account"
        title="Admin"
        to="/admin"
      />
      <v-list-item
        v-if="!user"
        prepend-icon="mdi-account-plus"
        title="Reģistrēties"
        to="/register"
      />
    </v-list>
  </v-card>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue"
import axios from "axios"
import { API_URL } from "@/services/api"

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  initials: {
    type: String,
    default: "U",
  },
})

const conversations = ref([])
const exchanges = ref([])
let notificationsInterval = null

const newMessagesCount = computed(() => {
  return conversations.value.filter((conversation) => {
    return conversation.has_unread_messages
  }).length
})

const newExchangeRequestsCount = computed(() => {
  const seenAt = Number(localStorage.getItem("exchanges_seen_at") || 0)

  return exchanges.value.filter((exchange) => {
    return exchange.status === "pending" &&
      Number(exchange.owner_id) === Number(props.user?.id) &&
      new Date(exchange.created_at).getTime() > seenAt
  }).length
})

onMounted(() => {
  fetchNotifications()

  notificationsInterval = setInterval(fetchNotifications, 15000)
})

onBeforeUnmount(() => {
  if (notificationsInterval) clearInterval(notificationsInterval)
})

watch(() => props.user?.id, () => {
  fetchNotifications()
})

function authHeaders() {
  return {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  }
}

async function fetchNotifications() {
  if (!props.user?.id || !localStorage.getItem("token")) return

  try {
    const [conversationsResponse, exchangesResponse] = await Promise.all([
      axios.get(`${API_URL}/conversations`, { headers: authHeaders() }),
      axios.get(`${API_URL}/exchanges`, { headers: authHeaders() }),
    ])

    conversations.value = conversationsResponse.data
    exchanges.value = exchangesResponse.data
  } catch (error) {
    console.error(error)
  }
}
</script>

<style scoped>
.panel-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.sidebar-profile-link {
  color: inherit;
  text-decoration: none;
  border-radius: 8px;
  transition: background-color 0.15s ease;
}

.sidebar-profile-link:hover {
  background: rgba(var(--v-theme-on-surface), 0.06);
}
</style>
