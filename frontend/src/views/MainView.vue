<template>
  <div ref="mainPage" class="locked-page">
    <AppHeader :user="userState.user" :initials="initials" />

    <v-container fluid class="pa-0 locked-page__content">
      <v-container class="py-6 locked-page__shell" style="max-width: 1200px;">
        <v-row class="locked-page__row">
          <!-- Left Sidebar -->
          <v-col cols="12" md="3" class="d-none d-md-block locked-page__side">
            <AppSidebar :user="userState.user" :initials="initials" @logout="handleLogout" />

            <v-card v-if="!isGuest" rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-2 font-weight-bold mb-3">Jūsu grāmatas</div>

              <div v-if="loadingMyBooks" class="py-2">
                <v-progress-circular indeterminate color="primary" size="22" />
              </div>

              <div v-else-if="myBookStats.length" class="d-flex gap-2 flex-wrap">
                <v-chip
                  v-for="stat in myBookStats"
                  :key="stat.genre"
                  size="small"
                  :color="stat.color"
                  variant="tonal"
                >
                  {{ stat.genre }} ({{ stat.count }})
                </v-chip>
              </div>

              <div v-else class="text-body-2 text-medium-emphasis">
                Tev vēl nav pievienotu grāmatu.
                <v-btn
                  to="/library"
                  variant="text"
                  color="primary"
                  size="small"
                  class="px-0 mt-1"
                >
                  Pievienot grāmatas
                </v-btn>
              </div>
            </v-card>
          </v-col>

          <!-- Main Content -->
          <v-col cols="12" md="6" class="locked-page__scroll locked-page__scroll--framed">
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
          <MainRightSidebar class="locked-page__side" />
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
import { computed, ref, onMounted, watch } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"
import { userState, initials, logout } from "@/stores/userStore"
import { API_URL } from "@/services/api"
import { usePageLock } from "@/composables/usePageLock"
import { useScrollForwarding } from "@/composables/useScrollForwarding"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"
import FeedTab from "@/components/main/FeedTab.vue"
import ExchangeBooksTab from "@/components/main/ExchangeBooksTab.vue"
import ExchangeRequestDialog from "@/components/main/ExchangeRequestDialog.vue"
import MainRightSidebar from "@/components/main/MainRightSidebar.vue"

const router = useRouter()
const mainPage = ref(null)
const mainContentScroll = ref(null)

usePageLock()

useScrollForwarding({
  source: mainPage,
  target: mainContentScroll,
})

function handleLogout() {
  logout(router)
}

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

onMounted(() => {
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

const activeTab = ref("feed")

watch(activeTab, (tab) => {
  if (tab === "exchange" && !requireAuth()) {
    activeTab.value = "feed"
    return
  }

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
const loadingMyBooks = ref(false)

const chipColors = ["primary", "secondary", "success", "warning", "info", "grey"]

const myBookStats = computed(() => {
  const counts = myBooks.value.reduce((result, book) => {
    const genre = book.genre || "Bez žanra"
    result[genre] = (result[genre] || 0) + 1
    return result
  }, {})

  return Object.entries(counts)
    .map(([genre, count], index) => ({
      genre,
      count,
      color: chipColors[index % chipColors.length],
    }))
    .sort((a, b) => b.count - a.count || a.genre.localeCompare(b.genre))
    .slice(0, 6)
})

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")


async function fetchMyBooks() {
  if (isGuest.value) return

  loadingMyBooks.value = true

  try {
    const response = await axios.get(`${API_URL}/books`, {
      headers: authHeaders(),
    })

    myBooks.value = response.data
  } catch (error) {
    console.error(error)
  } finally {
    loadingMyBooks.value = false
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

.panel-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.gap-2 {
  gap: 8px;
}

@media (max-width: 959px) {
  .main-content-scroll {
    overflow: visible;
    padding-top: 0;
  }
}
</style>
