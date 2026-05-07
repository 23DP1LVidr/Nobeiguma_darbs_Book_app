<template>
  <div class="main-page">
    <AppHeader :user="userState.user" :initials="initials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6 main-shell" style="max-width: 1200px;">
        <v-row class="main-layout-row">
          <!-- Left Sidebar -->
          <v-col cols="12" md="3" class="d-none d-md-block sidebar-col">
            <AppSidebar :user="userState.user" :initials="initials" @logout="handleLogout" />

            <v-card v-if="!isGuest" rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-2 font-weight-bold mb-3">Jūsu grāmatas</div>

              <div class="d-flex gap-2 flex-wrap">
                <v-chip size="small" color="primary" variant="tonal">Fantāzija (4)</v-chip>
                <v-chip size="small" color="secondary" variant="tonal">Detektīvs (3)</v-chip>
                <v-chip size="small" color="grey" variant="tonal">Zinātne (2)</v-chip>
              </div>
            </v-card>
          </v-col>

          <!-- Main Content -->
          <v-col cols="12" md="6" class="main-scroll-col">
            <v-tabs v-model="activeTab" color="primary" align-tabs="start" class="main-tabs mb-4">
              <v-tab value="feed" class="text-none">
                <v-icon start>mdi-newspaper</v-icon>
                Plūsma
              </v-tab>

              <v-tab value="exchange" class="text-none">
                <v-icon start>mdi-swap-horizontal</v-icon>
                Apmaiņa
              </v-tab>
            </v-tabs>

            <div ref="mainContentScroll" class="main-content-scroll">
              <v-window v-model="activeTab" class="main-content-window">
                <v-window-item value="feed">
                  <FeedTab
                    :user="userState.user"
                    :initials="initials"
                    :posts="posts"
                    :loading="loadingPosts"
                    :creating="creatingPost"
                    @create="createPost"
                    @toggle-like="togglePostLike"
                    @comment="createPostComment"
                    @delete-post="deletePost"
                    @delete-comment="deleteComment"
                  />
                </v-window-item>

                <v-window-item value="exchange">
                  <ExchangeBooksTab
                    :books="availableBooks"
                    :loading="loadingAvailableBooks"
                    :genres="genres"
                    :conditions="conditions"
                    @exchange="openExchangeDialog"
                  />
                </v-window-item>
              </v-window>
            </div>
          </v-col>

          <!-- Right Sidebar -->
          <MainRightSidebar class="sidebar-col" />
        </v-row>
      </v-container>
    </v-container>
    <ExchangeRequestDialog
      v-model="showExchangeDialog"
      :selected-book="selectedBook"
      :my-books="myBooks"
      :creating="creatingExchange"
      @submit="createExchangeRequest"
    />


    <v-snackbar v-model="showSnackbar" :color="snackbarColor" timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>
    <v-snackbar
      v-model="showProfileReminder"
      location="top right"
      timeout="-1"
      elevation="6"
      class="mt-4 mr-4"
      color="surface"
    >
      <div :class="$vuetify.theme.current.dark ? 'text-white' : 'text-black'">
        
        <div class="text-subtitle-1 font-weight-bold mb-2">
          Pabeidz savu profilu
        </div>

        <div class="text-body-2 mb-3">
          Lai citi lietotāji varētu labāk ar tevi sazināties,
          lūdzu aizpildi savu profilu.
        </div>

        <div class="d-flex justify-end gap-2">
          <v-btn size="small" variant="text" @click="showProfileReminder = false">
            Vēlāk
          </v-btn>

          <v-btn
            size="small"
            color="primary"
            @click="$router.push('/profile')"
          >
            Aizpildīt
          </v-btn>
        </div>

      </div>
    </v-snackbar>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, onMounted, watch } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"
import { userState, initials, logout } from "@/stores/userStore"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"
import FeedTab from "@/components/main/FeedTab.vue"
import ExchangeBooksTab from "@/components/main/ExchangeBooksTab.vue"
import ExchangeRequestDialog from "@/components/main/ExchangeRequestDialog.vue"
import MainRightSidebar from "@/components/main/MainRightSidebar.vue"

const router = useRouter()

function handleLogout() {
  logout(router)
}

const API_URL = "http://127.0.0.1:8000/api"

function authHeaders() {
  return {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  }
}

const isGuest = computed(() => !localStorage.getItem("token"))

function requestConfig() {
  return isGuest.value ? {} : { headers: authHeaders() }
}

function requireAuth() {
  if (!isGuest.value) return true

  router.push("/register")
  return false
}

const showProfileReminder = ref(false)
const mainContentScroll = ref(null)

onMounted(() => {
  document.documentElement.classList.add("main-page-locked")
  document.body.classList.add("main-page-locked")

  // Add global scroll listener to allow scrolling from any part of the page
  const handleWheel = (event) => {
    // Skip if the event target is inside scrollable elements that should handle their own scrolling
    if (event.target.closest('.v-menu, .v-dialog, .v-overlay, .v-select__menu, .v-autocomplete__menu')) {
      return
    }

    // Prevent default page scrolling
    event.preventDefault()

    // Scroll the main content area instead
    if (mainContentScroll.value) {
      mainContentScroll.value.scrollTop += event.deltaY
    }
  }

  document.addEventListener('wheel', handleWheel, { passive: false })

  // Store the listener for cleanup
  window.mainPageWheelListener = handleWheel

  fetchPosts()
  if (!isGuest.value) {
    fetchMyBooks()
  }

  const alreadyShown = localStorage.getItem("profile_reminder_shown")
  if (!alreadyShown && !isGuest.value) {
    const user = userState.user

    if (!user?.city || !user?.about || !user?.avatar) {
      showProfileReminder.value = true
      localStorage.setItem("profile_reminder_shown", "true")
    }
  }
})

onBeforeUnmount(() => {
  document.documentElement.classList.remove("main-page-locked")
  document.body.classList.remove("main-page-locked")

  // Clean up the global scroll listener
  if (window.mainPageWheelListener) {
    document.removeEventListener('wheel', window.mainPageWheelListener)
    window.mainPageWheelListener = null
  }
})
const activeTab = ref("feed")

watch(activeTab, (tab) => {
  if (tab === "exchange" && !loadedAvailableBooks.value) {
    fetchAvailableBooks()
  }
})

const genres = [
  "Visi",
  "Fantāzija",
  "Romāns",
  "Detektīvs",
  "Zinātne",
  "Pašpalīdzība",
  "Dzeja",
  "Klasika",
  "Vēsture",
  "Biogrāfija",
]

const conditions = [
  "Visi",
  "Jauna",
  "Labā stāvoklī",
  "Vidējā stāvoklī",
  "Sliktā stāvoklī",
]

const availableBooks = ref([])
const loadingAvailableBooks = ref(false)
const loadedAvailableBooks = ref(false)

const posts = ref([])
const loadingPosts = ref(false)
const creatingPost = ref(false)

async function fetchPosts() {
  loadingPosts.value = true

  try {
    const response = await axios.get(`${API_URL}/posts`, requestConfig())

    posts.value = response.data
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās ielādēt ierakstus.", "error")
  } finally {
    loadingPosts.value = false
  }
}

async function createPost(post) {
  if (!requireAuth()) return

  creatingPost.value = true

  try {
    const response = await axios.post(
      `${API_URL}/posts`,
      {
        content: post.content,
        image: post.image,
      },
      {
        headers: authHeaders(),
      }
    )

    posts.value = [response.data, ...posts.value]
    showMessage("Ieraksts publicēts.", "success")
  } catch (error) {
    console.error(error)
    showMessage(error.response?.data?.message || "Neizdevās publicēt ierakstu.", "error")
  } finally {
    creatingPost.value = false
  }
}

async function togglePostLike(post) {
  if (!requireAuth()) return

  try {
    const response = await axios.post(
      `${API_URL}/posts/${post.id}/like`,
      {},
      {
        headers: authHeaders(),
      }
    )

    replacePost(response.data)
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās atjaunināt patīk atzīmi.", "error")
  }
}

async function createPostComment({ post, content }) {
  if (!requireAuth()) return

  try {
    const response = await axios.post(
      `${API_URL}/posts/${post.id}/comments`,
      { content },
      {
        headers: authHeaders(),
      }
    )

    replacePost(response.data)
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās pievienot komentāru.", "error")
  }
}

async function deletePost(post) {
  if (!requireAuth()) return

  try {
    await axios.delete(`${API_URL}/posts/${post.id}`, {
      headers: authHeaders(),
    })

    posts.value = posts.value.filter((item) => item.id !== post.id)
    showMessage("Ieraksts dzēsts.", "success")
  } catch (error) {
    console.error(error)
    showMessage(error.response?.data?.message || "Neizdevās dzēst ierakstu.", "error")
  }
}

async function deleteComment({ post, comment }) {
  if (!requireAuth()) return

  try {
    const response = await axios.delete(
      `${API_URL}/posts/${post.id}/comments/${comment.id}`,
      {
        headers: authHeaders(),
      }
    )

    replacePost(response.data)
    showMessage("Komentārs dzēsts.", "success")
  } catch (error) {
    console.error(error)
    showMessage(error.response?.data?.message || "Neizdevās dzēst komentāru.", "error")
  }
}

function replacePost(updatedPost) {
  posts.value = posts.value.map((post) => {
    return post.id === updatedPost.id ? updatedPost : post
  })
}

async function fetchAvailableBooks() {
  loadingAvailableBooks.value = true

  try {
    const response = await axios.get(`${API_URL}/books/available`, requestConfig())

    availableBooks.value = response.data
    loadedAvailableBooks.value = true
  } catch (error) {
    console.error(error)
  } finally {
    loadingAvailableBooks.value = false
  }
}

const showExchangeDialog = ref(false)
const selectedBook = ref(null)
const creatingExchange = ref(false)

const myBooks = ref([])

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")


async function fetchMyBooks() {
  if (isGuest.value) return

  try {
    const response = await axios.get(`${API_URL}/books`, {
      headers: authHeaders(),
    })

    myBooks.value = response.data
  } catch (error) {
    console.error(error)
  }
}

function openExchangeDialog(book) {
  if (!requireAuth()) return

  selectedBook.value = book
  showExchangeDialog.value = true
}

async function createExchangeRequest(offeredBookId) {
  if (!requireAuth()) return
  if (!selectedBook.value) return

  creatingExchange.value = true

  try {
    await axios.post(
      `${API_URL}/exchanges`,
      {
        requested_book_id: selectedBook.value.id,
        offered_book_id: offeredBookId,
      },
      {
        headers: authHeaders(),
      }
    )

    showExchangeDialog.value = false

    showMessage("Apmaiņas pieprasījums nosūtīts.", "success")

  } catch (error) {
    console.error(error)

    showMessage(
      error.response?.data?.message || "Neizdevās nosūtīt apmaiņas pieprasījumu.",
      "error"
    )
  } finally {
    creatingExchange.value = false
  }
}


function showMessage(message, color = "success") {
  snackbarMessage.value = message
  snackbarColor.value = color
  showSnackbar.value = true
}

</script>

<style scoped>
:global(html.main-page-locked),
:global(body.main-page-locked) {
  height: 100%;
  overflow: hidden !important;
}

:global(body.main-page-locked #app),
:global(body.main-page-locked .v-application),
:global(body.main-page-locked .v-application__wrap),
:global(body.main-page-locked .v-main) {
  height: 100%;
  min-height: 0;
  overflow: hidden;
}

.main-page {
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

.main-shell {
  height: 100%;
  box-sizing: border-box;
}

.main-layout-row {
  height: 100%;
  min-height: 0;
}

.main-scroll-col {
  height: 100%;
  min-height: 0;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.main-tabs {
  flex: 0 0 auto;
  z-index: 5;
  background: rgb(var(--v-theme-background));
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.main-content-scroll {
  flex: 1 1 auto;
  min-height: 0;
  overflow-y: auto;
  padding-top: 16px;
  padding-bottom: 32px;
  scrollbar-gutter: stable;
  overscroll-behavior: contain;
}

.sidebar-col {
  align-self: flex-start;
  position: sticky;
  top: 24px;
  max-height: calc(100vh - 48px);
  overflow: hidden;
}

.panel-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.gap-2 {
  gap: 8px;
}

@media (max-width: 959px) {
  .main-page,
  .page-content {
    height: auto;
    min-height: 100vh;
    overflow: visible;
  }

  .main-scroll-col {
    height: auto;
    overflow: visible;
    display: block;
  }

  .main-content-scroll {
    overflow: visible;
    padding-top: 0;
  }
}
</style>
