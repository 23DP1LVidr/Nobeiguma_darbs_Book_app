<template>
  <div class="messages-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6 messages-shell" style="max-width: 1200px;">
        <v-row class="messages-layout-row">
          <v-col cols="12" md="3" class="d-none d-md-block sidebar-col">
            <AppSidebar :user="user" :initials="initials" @logout="logoutUser" />
          </v-col>

          <v-col
            cols="12"
            md="6"
            class="messages-scroll-col"
            :class="{ 'd-none d-md-flex': !selectedChat }"
          >
            <MessageChatPanel
              v-model:new-message="newMessage"
              :selected-chat="selectedChat"
              :messages="messages"
              :loading="loadingMessages"
              :current-user-id="user?.id"
              :user-initials="userInitials"
              @send="sendMessage"
              @back="closeMobileChat"
            />
          </v-col>

          <v-col
            cols="12"
            md="3"
            class="messages-side-col"
            :class="{ 'd-none d-md-block': selectedChat }"
          >
            <ConversationListPanel
              v-model:search="search"
              :chats="filteredChats"
              :selected-chat="selectedChat"
              :loading="loadingChats"
              :user-initials="userInitials"
              @select="selectChat"
            />
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
import { computed, onBeforeUnmount, onMounted, ref } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"
import ConversationListPanel from "@/components/messages/ConversationListPanel.vue"
import MessageChatPanel from "@/components/messages/MessageChatPanel.vue"

const router = useRouter()
const route = useRoute()

const API_URL = "http://127.0.0.1:8000/api"

const user = ref(JSON.parse(localStorage.getItem("user")) || null)
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

const initials = computed(() => userInitials(user.value))

const filteredChats = computed(() => {
  const query = search.value.toLowerCase()

  return chats.value.filter((chat) => {
    const fullName = `${chat.otherUser?.name || ""} ${chat.otherUser?.surname || ""}`.toLowerCase()
    return fullName.includes(query)
  })
})

onMounted(async () => {
  document.documentElement.classList.add("messages-page-locked")
  document.body.classList.add("messages-page-locked")

  await fetchChats()
  await selectInitialChat()

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
  document.documentElement.classList.remove("messages-page-locked")
  document.body.classList.remove("messages-page-locked")

  if (messagesInterval) clearInterval(messagesInterval)
  if (chatsInterval) clearInterval(chatsInterval)
})

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

async function selectInitialChat() {
  const conversationId = Number(route.query.conversation)

  if (!conversationId) return

  const chat = chats.value.find((item) => Number(item.id) === conversationId)

  if (chat) {
    await selectChat(chat)
  }
}

async function fetchChats(showLoading = true) {
  if (showLoading) loadingChats.value = true

  try {
    const response = await axios.get(`${API_URL}/conversations`, {
      headers: authHeaders(),
    })

    chats.value = response.data.map(normalizeConversation)
    syncSelectedChat()
  } catch (error) {
    console.error(error)

    if (showLoading) {
      showMessage("Neizdevās ielādēt sarunas.", "error")
    }
  } finally {
    if (showLoading) loadingChats.value = false
  }
}

function normalizeConversation(conversation) {
  const isUserOne = Number(conversation.user_one_id) === Number(user.value?.id)
  const otherUser = isUserOne
    ? conversation.user_two
    : conversation.user_one

  const lastMessageObj = conversation.latest_message || conversation.messages?.[0]
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
    hasNewMessage: Boolean(conversation.has_unread_messages),
  }
}

function markConversationSeen(conversationId) {
  chats.value = chats.value.map((chat) => {
    return Number(chat.id) === Number(conversationId)
      ? { ...chat, hasNewMessage: false }
      : chat
  })
}

function syncSelectedChat() {
  if (!selectedChat.value) return

  const updatedSelectedChat = chats.value.find(
    (chat) => Number(chat.id) === Number(selectedChat.value.id)
  )

  if (updatedSelectedChat) {
    selectedChat.value = updatedSelectedChat
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

function closeMobileChat() {
  selectedChat.value = null
  messages.value = []

  router.replace({
    path: "/messages",
  })
}

async function fetchMessages(conversationId, showLoading = true) {
  if (showLoading) loadingMessages.value = true

  try {
    const response = await axios.get(`${API_URL}/conversations/${conversationId}/messages`, {
      headers: authHeaders(),
    })

    messages.value = response.data
    markConversationSeen(conversationId)
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
:global(html.messages-page-locked),
:global(body.messages-page-locked) {
  height: 100%;
  overflow: hidden !important;
}

:global(body.messages-page-locked #app),
:global(body.messages-page-locked .v-application),
:global(body.messages-page-locked .v-application__wrap),
:global(body.messages-page-locked .v-main) {
  height: 100%;
  min-height: 0;
  overflow: hidden;
}

.messages-page {
  height: 100vh;
  background: rgb(var(--v-theme-background));
  color: rgb(var(--v-theme-on-background));
  overflow: hidden;
}

.page-content {
  height: calc(100vh - 64px);
  background: rgb(var(--v-theme-background));
  overflow: hidden;
}

.messages-shell {
  height: 100%;
  box-sizing: border-box;
}

.messages-layout-row {
  height: 100%;
  min-height: 0;
}

.messages-scroll-col {
  height: 100%;
  min-height: 0;
  overflow-y: auto;
  padding-bottom: 32px;
  scrollbar-gutter: stable;
  display: flex;
  flex-direction: column;
}

.sidebar-col,
.messages-side-col {
  align-self: flex-start;
  position: sticky;
  top: 24px;
  max-height: calc(100vh - 48px);
  overflow: hidden;
}

@media (max-width: 959px) {
  :global(html.messages-page-locked),
  :global(body.messages-page-locked) {
    height: auto;
    overflow: visible !important;
  }

  .messages-page,
  .page-content {
    height: calc(100vh - 64px);
    min-height: 100vh;
    overflow: hidden;
  }

  .messages-scroll-col {
    height: 100%;
    overflow: hidden;
    padding-bottom: 0;
  }

  .messages-shell,
  .messages-layout-row,
  .messages-side-col {
    height: 100%;
    min-height: 0;
  }

  .messages-side-col {
    max-height: none;
    overflow-y: auto;
    position: static;
  }
}
</style>
