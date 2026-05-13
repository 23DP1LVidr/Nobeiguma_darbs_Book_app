<template>
  <div class="locked-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 locked-page__content">
      <v-container class="py-6 locked-page__shell" style="max-width: 1200px;">
        <v-row class="locked-page__row">
          <ExchangeStatsSidebar
            class="locked-page__side"
            :user="user"
            :initials="initials"
            :pending-count="pendingExchanges.length"
            :active-count="activeExchanges.length"
            :completed-count="completedExchanges.length"
            @logout="logoutUser"
          />

          <v-col cols="12" md="6" class="locked-page__scroll">
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

          <ExchangeQuickActions class="locked-page__side" />
        </v-row>
      </v-container>
    </v-container>

    <v-snackbar v-model="showSnackbar" :color="snackbarColor" timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"
import { API_URL } from "@/services/api"
import { usePageLock } from "@/composables/usePageLock"
import AppHeader from "@/components/layout/AppHeader.vue"
import ExchangeListPanel from "@/components/exchanges/ExchangeListPanel.vue"
import ExchangeQuickActions from "@/components/exchanges/ExchangeQuickActions.vue"
import ExchangeStatsSidebar from "@/components/exchanges/ExchangeStatsSidebar.vue"

const router = useRouter()

usePageLock()

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
  localStorage.setItem("exchanges_seen_at", String(Date.now()))
  fetchExchanges()
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
