<template>
  <div class="exchanges-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6" style="max-width: 1200px;">
        <v-row>
          <v-col cols="12" md="3" class="d-none d-md-block">
            <AppSidebar :user="user" :initials="initials" @logout="logoutUser" />

            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-2 font-weight-bold mb-3">Apmaiņu statistika</div>

              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Gaida</span>
                <span class="text-body-2 font-weight-bold">{{ pendingExchanges.length }}</span>
              </div>

              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Aktīvas</span>
                <span class="text-body-2 font-weight-bold">{{ activeExchanges.length }}</span>
              </div>

              <div class="d-flex justify-space-between">
                <span class="text-body-2">Pabeigtas</span>
                <span class="text-body-2 font-weight-bold">{{ completedExchanges.length }}</span>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="6">
            <div class="d-flex justify-space-between align-center mb-6">
              <div>
                <h1 class="text-h4 font-weight-bold">Manas apmaiņas</h1>
                <p class="text-body-1 text-medium-emphasis">
                  Sekojiet līdzi savām grāmatu apmaiņām
                </p>
              </div>

              <v-btn color="primary" prepend-icon="mdi-magnify" to="/main">
                Atrast grāmatas
              </v-btn>
            </div>

            <v-tabs v-model="activeTab" color="primary" class="mb-4">
              <v-tab value="pending">Gaida <span class="ml-2">({{ pendingExchanges.length }})</span></v-tab>
              <v-tab value="active">Aktīvas <span class="ml-2">({{ activeExchanges.length }})</span></v-tab>
              <v-tab value="completed">Pabeigtas <span class="ml-2">({{ completedExchanges.length }})</span></v-tab>
            </v-tabs>

            <div v-if="loadingExchanges" class="text-center py-10">
              <v-progress-circular indeterminate color="primary" />
            </div>

            <v-window v-else v-model="activeTab">
              <v-window-item value="pending">
                <div v-if="pendingExchanges.length === 0" class="text-center py-10">
                  <v-icon size="56" color="grey">mdi-swap-horizontal</v-icon>
                  <div class="text-h6 mt-3">Nav gaidošu apmaiņu</div>
                </div>

                <ExchangeCard
                  v-for="exchange in pendingExchanges"
                  :key="exchange.id"
                  :exchange="exchange"
                  :current-user-id="Number(user?.id)"
                  pending
                  @accept="acceptExchange"
                  @reject="rejectExchange"
                  @cancel="cancelExchange"
                />
              </v-window-item>

              <v-window-item value="active">
                <div v-if="activeExchanges.length === 0" class="text-center py-10">
                  <v-icon size="56" color="grey">mdi-handshake</v-icon>
                  <div class="text-h6 mt-3">Nav aktīvu apmaiņu</div>
                </div>

                <ExchangeCard
                  v-for="exchange in activeExchanges"
                  :key="exchange.id"
                  :exchange="exchange"
                  :current-user-id="Number(user?.id)"
                  active
                  @complete="completeExchange"
                />
              </v-window-item>

              <v-window-item value="completed">
                <div v-if="completedExchanges.length === 0" class="text-center py-10">
                  <v-icon size="56" color="grey">mdi-check-circle-outline</v-icon>
                  <div class="text-h6 mt-3">Nav pabeigtu apmaiņu</div>
                </div>

                <ExchangeCard
                  v-for="exchange in completedExchanges"
                  :key="exchange.id"
                  :exchange="exchange"
                  :current-user-id="Number(user?.id)"
                  completed
                />
              </v-window-item>
            </v-window>
          </v-col>

          <v-col cols="12" md="3" class="d-none d-md-block">
            <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Ātrās darbības</div>
              <v-btn block variant="tonal" color="primary" class="mb-2" prepend-icon="mdi-magnify" to="/main">
                Pārlūkot piedāvājumus
              </v-btn>
              <v-btn block variant="outlined" prepend-icon="mdi-book-open-variant" to="/library">
                Mana bibliotēka
              </v-btn>
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
import { ref, computed, onMounted } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"
import ExchangeCard from "@/components/ExchangeCard.vue"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"

const router = useRouter()

const user = ref(JSON.parse(localStorage.getItem("user")) || null)

const initials = computed(() => {
  const nameInitial = user.value?.name?.[0] || ""
  const surnameInitial = user.value?.surname?.[0] || ""
  return `${nameInitial}${surnameInitial}`.toUpperCase() || "U"
})

function logoutUser() {
  localStorage.removeItem("token")
  localStorage.removeItem("user")
  router.replace("/login")
}

const API_URL = "http://127.0.0.1:8000/api"

function authHeaders() {
  return {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  }
}

const activeTab = ref("pending")
const exchanges = ref([])
const loadingExchanges = ref(false)

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")

const pendingExchanges = computed(() =>
  exchanges.value.filter((exchange) => exchange.status === "pending")
)

const activeExchanges = computed(() =>
  exchanges.value.filter((exchange) => exchange.status === "accepted")
)

const completedExchanges = computed(() =>
  exchanges.value.filter((exchange) => exchange.status === "completed")
)

onMounted(() => {
  fetchExchanges()
})

async function fetchExchanges() {
  loadingExchanges.value = true

  try {
    const response = await axios.get(`${API_URL}/exchanges`, {
      headers: authHeaders(),
    })

    exchanges.value = response.data
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās ielādēt apmaiņas.", "error")
  } finally {
    loadingExchanges.value = false
  }
}

async function acceptExchange(exchangeId) {
  try {
    const response = await axios.put(`${API_URL}/exchanges/${exchangeId}/accept`, {}, {
      headers: authHeaders(),
    })

    const conversationId = response.data.conversation_id

    router.push({
      path: "/messages",
      query: { conversation: conversationId },
    })
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās apstiprināt apmaiņu.", "error")
  }
}

async function rejectExchange(exchangeId) {
  try {
    await axios.put(`${API_URL}/exchanges/${exchangeId}/reject`, {}, {
      headers: authHeaders(),
    })

    exchanges.value = exchanges.value.filter(
      (exchange) => exchange.id !== exchangeId
    )
    showMessage("Apmaiņa noraidīta.", "success")
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās noraidīt apmaiņu.", "error")
  }
}

async function completeExchange(exchangeId) {
  try {
    await axios.put(`${API_URL}/exchanges/${exchangeId}/complete`, {}, {
      headers: authHeaders(),
    })

    exchanges.value = exchanges.value.filter(
      (exchange) => exchange.id !== exchangeId
    )
    showMessage("Apmaiņa pabeigta.", "success")
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās pabeigt apmaiņu.", "error")
  }
}

async function cancelExchange(exchangeId) {
  try {
    await axios.delete(`${API_URL}/exchanges/${exchangeId}`, {
      headers: authHeaders(),
    })

    exchanges.value = exchanges.value.filter(
      (exchange) => exchange.id !== exchangeId
    )
    showMessage("Apmaiņa atcelta.", "success")
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās atcelt apmaiņu.", "error")
  }
}

function showMessage(message, color = "success") {
  snackbarMessage.value = message
  snackbarColor.value = color
  showSnackbar.value = true
}
</script>

<style scoped>
.exchanges-page {
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
  color: rgb(var(--v-theme-on-surface)) !important;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.panel-card,
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.exchange-books {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.book-placeholder {
  width: 60px;
  height: 90px;
  border-radius: 8px;
  background: rgba(var(--v-theme-on-surface), 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>