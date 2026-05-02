<template>
  <div class="messages-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6" style="max-width: 1200px;">
        <v-row>
          <v-col cols="12" md="3" class="d-none d-md-block">
            <AppSidebar :user="user" :initials="initials" @logout="logoutUser" />
          </v-col>

          <v-col cols="12" md="6">
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
                <div v-if="loadingMessages" class="text-center py-8">
                  <v-progress-circular indeterminate color="primary" />
                </div>

                <template v-else>

                  <!-- MESSAGES -->
                  <div
                    v-for="message in messages"
                    :key="message.id"
                  >
                    <!-- SYSTEM MESSAGE -->
                    <div v-if="message.user_id === null" class="message-row">
                      <div class="system-message">
                        {{ message.message }}
                      </div>
                    </div>

                    <!-- USER MESSAGE -->
                    <div
                      v-else
                      class="message-row"
                      :class="{ mine: message.user_id === user?.id }"
                    >
                      <div class="message-bubble" :class="{ mine: message.user_id === user?.id }">
                        {{ message.message }}
                      </div>
                    </div>
                  </div>
                </template>
              </div>

              <v-divider />

              <div class="message-input">
                <v-text-field
                  v-model="newMessage"
                  placeholder="Rakstīt ziņu..."
                  variant="outlined"
                  density="compact"
                  hide-details
                  class="search-field"
                  :disabled="!selectedChat"
                  @keyup.enter="sendMessage"
                />

                <v-btn icon color="primary" class="ml-2" :disabled="!selectedChat" @click="sendMessage">
                  <v-icon>mdi-send</v-icon>
                </v-btn>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="3">
            <v-card rounded="xl" elevation="0" class="pa-4 content-card">
              <div class="text-subtitle-1 font-weight-bold mb-4">Sarunas</div>

              <v-text-field
                v-model="search"
                placeholder="Meklēt..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                class="mb-4 search-field"
              />

              <div v-if="loadingChats" class="text-center py-6">
                <v-progress-circular indeterminate color="primary" />
              </div>

              <v-list v-else bg-color="transparent">
                <v-list-item
                  v-for="chat in filteredChats"
                  :key="chat.id"
                  :active="selectedChat?.id === chat.id"
                  rounded="xl"
                  class="mb-2"
                  @click="selectChat(chat)"
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
                </v-list-item>

                <div v-if="filteredChats.length === 0" class="text-center text-medium-emphasis py-6">
                  Nav aktīvu sarunu.
                </div>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>

    <v-snackbar v-model="showSnackbar" :color="snackbarColor" timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>
  </div>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"

const router = useRouter()
const route = useRoute()

const API_URL = "http://127.0.0.1:8000/api"

const user = ref(JSON.parse(localStorage.getItem("user")) || null)

const initials = computed(() => userInitials(user.value))

const search = ref("")
const newMessage = ref("")
const chats = ref([])
const selectedChat = ref(null)
const messages = ref([])

const loadingChats = ref(false)
const loadingMessages = ref(false)

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")

let messagesInterval = null
let chatsInterval = null

function userInitials(person) {
  return `${person?.name?.[0] || ""}${person?.surname?.[0] || ""}`.toUpperCase() || "U"
}

function authHeaders() {
  return {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  }
}

function logoutUser() {
  localStorage.removeItem("token")
  localStorage.removeItem("user")
  router.replace("/login")
}

const filteredChats = computed(() => {
  const query = search.value.toLowerCase()

  return chats.value.filter((chat) => {
    const fullName = `${chat.otherUser?.name || ""} ${chat.otherUser?.surname || ""}`.toLowerCase()
    return fullName.includes(query)
  })
})

onMounted(async () => {
  await fetchChats()

  const conversationId = Number(route.query.conversation)

  if (conversationId) {
    const chat = chats.value.find((item) => Number(item.id) === conversationId)

    if (chat) {
      await selectChat(chat)
    }
  } else if (chats.value.length > 0) {
    await selectChat(chats.value[0])
  }

  chatsInterval = setInterval(() => {
    fetchChats(false)
  }, 5000)

  messagesInterval = setInterval(() => {
    if (selectedChat.value) {
      fetchMessages(selectedChat.value.id, false)
    }
  }, 3000)
})

onBeforeUnmount(() => {
  if (messagesInterval) clearInterval(messagesInterval)
  if (chatsInterval) clearInterval(chatsInterval)
})

async function fetchChats(showLoading = true) {
  if (showLoading) loadingChats.value = true

  try {
    const response = await axios.get(`${API_URL}/conversations`, {
      headers: authHeaders(),
    })

    chats.value = response.data.map((conversation) => {
      const isUserOne = Number(conversation.user_one_id) === Number(user.value?.id)

      const otherUser = isUserOne
        ? conversation.user_two
        : conversation.user_one

      const lastMessageObj = conversation.messages?.[0]

      let lastMessage = "Nav ziņu"

      if (lastMessageObj) {
        const isMine = Number(lastMessageObj.user_id) === Number(user.value?.id)

        lastMessage = isMine
          ? `Jūs: ${lastMessageObj.message}`
          : lastMessageObj.message
      }

      return {
        ...conversation,
        otherUser,
        lastMessage,
      }
    })

    if (selectedChat.value) {
      const updatedSelectedChat = chats.value.find(
        (chat) => Number(chat.id) === Number(selectedChat.value.id)
      )

      if (updatedSelectedChat) {
        selectedChat.value = updatedSelectedChat
      }
    }
  } catch (error) {
    console.error(error)

    if (showLoading) {
      showMessage("Neizdevās ielādēt sarunas.", "error")
    }
  } finally {
    if (showLoading) loadingChats.value = false
  }
}

async function selectChat(chat) {
  selectedChat.value = chat

  router.replace({
    path: "/messages",
    query: { conversation: chat.id },
  })

  await fetchMessages(chat.id)
}

async function fetchMessages(conversationId, showLoading = true) {
  if (showLoading) loadingMessages.value = true

  try {
    const response = await axios.get(`${API_URL}/conversations/${conversationId}/messages`, {
      headers: authHeaders(),
    })

    messages.value = response.data
  } catch (error) {
    console.error(error)

    if (showLoading) {
      showMessage("Neizdevās ielādēt ziņas.", "error")
    }
  } finally {
    if (showLoading) loadingMessages.value = false
  }
}

async function sendMessage() {
  const text = newMessage.value.trim()

  if (!text || !selectedChat.value) return

  try {
    const response = await axios.post(
      `${API_URL}/conversations/${selectedChat.value.id}/messages`,
      { message: text },
      { headers: authHeaders() }
    )

    messages.value.push(response.data)
    newMessage.value = ""

    await fetchChats(false)
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās nosūtīt ziņu.", "error")
  }
}

function showMessage(message, color = "success") {
  snackbarMessage.value = message
  snackbarColor.value = color
  showSnackbar.value = true
}
</script>

<style scoped>
.messages-page {
  min-height: 100vh;
  background: rgb(var(--v-theme-background));
  color: rgb(var(--v-theme-on-background));
}

.page-content {
  min-height: 100vh;
  background: rgb(var(--v-theme-background));
}

.app-header {
  background: rgb(var(--v-theme-surface)) !important;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.panel-card,
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

.exchange-system-message {
  max-width: 420px;
  margin: 24px auto;
  padding: 14px 18px;
  border-radius: 16px;
  text-align: center;
  background: rgba(var(--v-theme-primary), 0.12);
  border: 1px solid rgba(var(--v-theme-primary), 0.25);
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