<template>
  <div class="exchanges-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6 exchanges-shell" style="max-width: 1200px;">
        <v-row class="exchanges-layout-row">
          <ExchangeStatsSidebar
            class="sidebar-col"
            :user="user"
            :initials="initials"
            :pending-count="pendingExchanges.length"
            :active-count="activeExchanges.length"
            :completed-count="completedExchanges.length"
            @logout="logoutUser"
          />

          <v-col cols="12" md="6" class="exchanges-scroll-col">
            <ExchangeListPanel
              :pending-exchanges="pendingExchanges"
              :active-exchanges="activeExchanges"
              :completed-exchanges="completedExchanges"
              :current-user-id="Number(user?.id)"
              :loading="loadingExchanges"
              @accept="acceptExchange"
              @reject="rejectExchange"
              @cancel="cancelExchange"
              @complete="completeExchange"
            />
          </v-col>

          <ExchangeQuickActions class="sidebar-col" />
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
import axios from "axios"
import { useRouter } from "vue-router"
import { API_URL } from "@/services/api"
import AppHeader from "@/components/layout/AppHeader.vue"
import ExchangeListPanel from "@/components/exchanges/ExchangeListPanel.vue"
import ExchangeQuickActions from "@/components/exchanges/ExchangeQuickActions.vue"
import ExchangeStatsSidebar from "@/components/exchanges/ExchangeStatsSidebar.vue"

const router = useRouter()

const user = ref(JSON.parse(localStorage.getItem("user")) || null)
const exchanges = ref([])
const loadingExchanges = ref(false)

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")

const initials = computed(() => {
  const nameInitial = user.value?.name?.[0] || ""
  const surnameInitial = user.value?.surname?.[0] || ""
  return `${nameInitial}${surnameInitial}`.toUpperCase() || "U"
})

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
  document.documentElement.classList.add("exchanges-page-locked")
  document.body.classList.add("exchanges-page-locked")
  localStorage.setItem("exchanges_seen_at", String(Date.now()))
  fetchExchanges()
})

onBeforeUnmount(() => {
  document.documentElement.classList.remove("exchanges-page-locked")
  document.body.classList.remove("exchanges-page-locked")
})

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

async function fetchExchanges() {
  loadingExchanges.value = true
  localStorage.setItem("exchanges_seen_at", String(Date.now()))

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

    router.push({
      path: "/messages",
      query: { conversation: response.data.conversation_id },
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

    removeExchange(exchangeId)
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

    removeExchange(exchangeId)
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

    removeExchange(exchangeId)
    showMessage("Apmaiņa atcelta.", "success")
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās atcelt apmaiņu.", "error")
  }
}

function removeExchange(exchangeId) {
  exchanges.value = exchanges.value.filter(
    (exchange) => exchange.id !== exchangeId
  )
}

function showMessage(message, color = "success") {
  snackbarMessage.value = message
  snackbarColor.value = color
  showSnackbar.value = true
}
</script>

<style scoped>
:global(html.exchanges-page-locked),
:global(body.exchanges-page-locked) {
  height: 100%;
  overflow: hidden !important;
}

:global(body.exchanges-page-locked #app),
:global(body.exchanges-page-locked .v-application),
:global(body.exchanges-page-locked .v-application__wrap),
:global(body.exchanges-page-locked .v-main) {
  height: 100%;
  min-height: 0;
  overflow: hidden;
}

.exchanges-page {
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

.exchanges-shell {
  height: 100%;
  box-sizing: border-box;
}

.exchanges-layout-row {
  height: 100%;
  min-height: 0;
}

.exchanges-scroll-col {
  height: 100%;
  min-height: 0;
  overflow-y: auto;
  padding-bottom: 32px;
  scrollbar-gutter: stable;
}

.sidebar-col {
  align-self: flex-start;
  position: sticky;
  top: 24px;
  max-height: calc(100vh - 48px);
  overflow: hidden;
}

@media (max-width: 959px) {
  :global(html.exchanges-page-locked),
  :global(body.exchanges-page-locked) {
    height: auto;
    overflow: visible !important;
  }

  .exchanges-page,
  .page-content {
    height: auto;
    min-height: 100vh;
    overflow: visible;
  }

  .exchanges-scroll-col {
    height: auto;
    overflow: visible;
  }
}
</style>
