<template>
  <div class="messages-page">
    <v-app-bar elevation="1" class="px-4 app-header">
      <div class="d-flex align-center" style="width: 280px;">
        <router-link to="/main" class="text-decoration-none">
          <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
        </router-link>
      </div>

      <v-spacer />

      <router-link to="/profile" class="text-decoration-none">
        <v-avatar size="36" color="primary">
          <v-img v-if="user?.avatar" :src="user.avatar" />
          <span v-else class="text-white text-caption">{{ initials }}</span>
        </v-avatar>
      </router-link>
    </v-app-bar>

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6" style="max-width: 1200px;">
        <v-row>
          <!-- Left Sidebar -->
          <v-col cols="12" md="3" class="d-none d-md-block">
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
                <v-list-item prepend-icon="mdi-account-group" title="Draugi" to="/friends" />
                <v-list-item prepend-icon="mdi-cog" title="Iestatījumi" to="/settings" />
                <v-list-item prepend-icon="mdi-logout" title="Izrakstīties" @click="logoutUser" />
              </v-list>
            </v-card>

            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-2 font-weight-bold mb-3">Jūsu grāmatas</div>

              <div class="d-flex gap-2 flex-wrap">
                <v-chip size="small" color="primary" variant="tonal">Fantāzija (4)</v-chip>
                <v-chip size="small" color="secondary" variant="tonal">Detektīvs (3)</v-chip>
                <v-chip size="small" color="grey" variant="tonal">Zinātne (2)</v-chip>
              </div>
            </v-card>
          </v-col>

        <!-- CHAT (CENTER) -->
        <v-col cols="12" md="6">
            <v-card rounded="xl" elevation="0" class="chat-card content-card">
            <!-- header -->
            <div class="chat-header">
                <div class="d-flex align-center">
                <v-avatar :color="selectedChat.color" class="mr-3">
                    <span class="text-white">{{ selectedChat.initials }}</span>
                </v-avatar>

                <div>
                    <div class="text-subtitle-1 font-weight-bold">
                    {{ selectedChat.name }}
                    </div>
                    <div class="text-caption text-medium-emphasis">
                    Aktīvs nesen
                    </div>
                </div>
                </div>
            </div>

            <v-divider />

            <!-- messages -->
            <div class="messages-area">
                <div
                v-for="message in selectedChat.messages"
                :key="message.id"
                class="message-row"
                :class="{ mine: message.mine }"
                >
                <div class="message-bubble" :class="{ mine: message.mine }">
                    {{ message.text }}
                </div>
                </div>
            </div>

            <v-divider />

            <!-- input -->
            <div class="message-input">
                <v-text-field
                v-model="newMessage"
                placeholder="Rakstīt ziņu..."
                variant="outlined"
                density="compact"
                hide-details
                class="search-field"
                @keyup.enter="sendMessage"
                />

                <v-btn icon color="primary" class="ml-2" @click="sendMessage">
                <v-icon>mdi-send</v-icon>
                </v-btn>
            </div>
            </v-card>
        </v-col>

        <!-- CHAT LIST (RIGHT) -->
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

            <v-list bg-color="transparent">
                <v-list-item
                v-for="chat in filteredChats"
                :key="chat.id"
                :active="selectedChat.id === chat.id"
                rounded="xl"
                class="mb-2"
                @click="selectedChat = chat"
                >
                <template #prepend>
                    <v-avatar :color="chat.color">
                    <span class="text-white">{{ chat.initials }}</span>
                    </v-avatar>
                </template>

                <v-list-item-title class="font-weight-bold">
                    {{ chat.name }}
                </v-list-item-title>

                <v-list-item-subtitle>
                    {{ chat.lastMessage }}
                </v-list-item-subtitle>

                <template #append>
                    <div class="text-caption text-medium-emphasis">
                    {{ chat.time }}
                    </div>
                </template>
                </v-list-item>
            </v-list>
            </v-card>
        </v-col>
        </v-row>
      </v-container>
    </v-container>
  </div>
</template>

<script setup>
import { computed, ref } from "vue"
import { useRouter } from "vue-router"

const router = useRouter()

// User data from localStorage
const user = ref(JSON.parse(localStorage.getItem("user")) || null)

// Computed initials
const initials = computed(() => {
  const nameInitial = user.value?.name?.[0] || ""
  const surnameInitial = user.value?.surname?.[0] || ""
  return `${nameInitial}${surnameInitial}`.toUpperCase() || "U"
})

// Logout function
function logoutUser() {
  localStorage.removeItem("token")
  localStorage.removeItem("user")
  router.replace("/login")
}

const search = ref("")
const newMessage = ref("")

const chats = ref([
  {
    id: 1,
    name: "Anna Kalniņa",
    initials: "AK",
    color: "secondary",
    lastMessage: "Super, tiksimies rīt!",
    time: "2h",
    messages: [
      { id: 1, text: "Sveiks! Vai grāmata vēl ir pieejama?", mine: false },
      { id: 2, text: "Jā, vēl ir pieejama.", mine: true },
      { id: 3, text: "Super, tiksimies rīt!", mine: false },
    ],
  },
  {
    id: 2,
    name: "Pēteris Mārtins",
    initials: "PM",
    color: "teal",
    lastMessage: "Varu piedāvāt arī 1984.",
    time: "5h",
    messages: [
      { id: 1, text: "Sveiks, interesē apmaiņa?", mine: false },
      { id: 2, text: "Jā, kādu grāmatu vari piedāvāt?", mine: true },
      { id: 3, text: "Varu piedāvāt arī 1984.", mine: false },
    ],
  },
  {
    id: 3,
    name: "Līga Vītola",
    initials: "LV",
    color: "orange",
    lastMessage: "Paldies par grāmatu!",
    time: "1d",
    messages: [
      { id: 1, text: "Paldies par grāmatu!", mine: false },
      { id: 2, text: "Prieks, ka noderēja!", mine: true },
    ],
  },
])

const selectedChat = ref(chats.value[0])

const filteredChats = computed(() => {
  return chats.value.filter((chat) =>
    chat.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

function sendMessage() {
  const text = newMessage.value.trim()

  if (!text) return

  selectedChat.value.messages.push({
    id: Date.now(),
    text,
    mine: true,
  })

  selectedChat.value.lastMessage = text
  selectedChat.value.time = "tagad"
  newMessage.value = ""
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
</style>