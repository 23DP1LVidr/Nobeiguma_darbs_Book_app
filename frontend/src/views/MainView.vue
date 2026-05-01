<template>
  <div class="main-page">
    <v-app-bar elevation="1" class="px-4 app-header">
      <div class="d-flex align-center" style="width: 280px;">
        <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
      </div>

      <v-spacer />

      <router-link to="/profile" class="text-decoration-none">
        <v-avatar size="36" color="primary" class="ml-2" style="cursor:pointer;">
          <v-img v-if="userState.user?.avatar" :src="userState.user.avatar" />
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
                  <v-img v-if="userState.user?.avatar" :src="userState.user.avatar" />
                  <span v-else class="text-white">{{ initials }}</span>
                </v-avatar>

                <div>
                  <div class="text-subtitle-1 font-weight-bold">
                    {{ userState.user?.name }} {{ userState.user?.surname }}
                  </div>
                  <div class="text-caption text-medium-emphasis">
                    @{{ userState.user?.username }}
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
                <v-list-item prepend-icon="mdi-logout" title="Izrakstīties" @click="handleLogout" />
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

          <!-- Main Content -->
          <v-col cols="12" md="6">
            <v-tabs v-model="activeTab" color="primary" align-tabs="start" class="mb-4">
              <v-tab value="feed" class="text-none">
                <v-icon start>mdi-newspaper</v-icon>
                Plūsma
              </v-tab>

              <v-tab value="exchange" class="text-none">
                <v-icon start>mdi-swap-horizontal</v-icon>
                Apmaiņa
              </v-tab>
            </v-tabs>

            <v-window v-model="activeTab">
              <!-- FEED TAB -->
              <v-window-item value="feed">
                <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
                  <div class="d-flex align-center">
                    <v-avatar size="40" color="primary" class="mr-3">
                      <v-img v-if="userState.user?.avatar" :src="userState.user.avatar" />
                      <span v-else class="text-white">{{ initials }}</span>
                    </v-avatar>

                    <v-text-field
                      v-model="newPost"
                      placeholder="Ko tu domā par grāmatām?"
                      variant="outlined"
                      density="compact"
                      hide-details
                      class="flex-grow-1 search-field"
                    />
                  </div>

                  <div class="d-flex justify-end mt-3 gap-2">
                    <v-btn variant="tonal" size="small" prepend-icon="mdi-image">Bilde</v-btn>
                    <v-btn color="primary" size="small">Publicēt</v-btn>
                  </div>
                </v-card>

                <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
                  <div class="d-flex align-center mb-3">
                    <v-avatar size="40" color="secondary" class="mr-3">
                      <span class="text-white">AK</span>
                    </v-avatar>

                    <div>
                      <div class="text-subtitle-2 font-weight-bold">Anna Kalniņa</div>
                      <div class="text-caption text-medium-emphasis">Pirms 2 stundām</div>
                    </div>
                  </div>

                  <p class="text-body-1 mb-3">
                    Tikko pabeidzu lasīt "Staburadzi" — brīnišķīga grāmata!
                    Autentisks stāsts par Latvijas dabu un cilvēkiem. Iesaku visiem!
                  </p>

                  <v-img
                    src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=600"
                    aspect-ratio="1.8"
                    class="rounded-lg mb-3"
                    cover
                  />

                  <div class="d-flex gap-4">
                    <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">Patīk</v-btn>
                    <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">Komentēt</v-btn>
                    <v-btn variant="text" size="small" prepend-icon="mdi-share-variant">Kopīgot</v-btn>
                  </div>
                </v-card>

                <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
                  <div class="d-flex align-center mb-3">
                    <v-avatar size="40" color="teal" class="mr-3">
                      <span class="text-white">PM</span>
                    </v-avatar>

                    <div>
                      <div class="text-subtitle-2 font-weight-bold">Pēteris Mārtins</div>
                      <div class="text-caption text-medium-emphasis">Pirms 5 stundām</div>
                    </div>
                  </div>

                  <div class="d-flex mb-3">
                    <v-img
                      src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=200"
                      width="80"
                      height="120"
                      class="rounded-lg mr-4"
                      cover
                    />

                    <div>
                      <div class="text-subtitle-1 font-weight-bold">1984</div>
                      <div class="text-body-2 text-medium-emphasis">Džordžs Orvels</div>
                      <v-rating :model-value="5" color="amber" density="compact" readonly size="small" />
                    </div>
                  </div>

                  <p class="text-body-1">
                    Otrā reize, kad lasu šo klasiku. Katru reizi atrodu ko jaunu.
                    Grāmata par brīvību, varu un patiesību — aktuāla arī mūsdienās.
                  </p>

                  <div class="d-flex gap-4 mt-3">
                    <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">Patīk (24)</v-btn>
                    <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">Komentēt (8)</v-btn>
                  </div>
                </v-card>

                <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
                  <div class="d-flex align-center mb-3">
                    <v-avatar size="40" color="orange" class="mr-3">
                      <span class="text-white">LV</span>
                    </v-avatar>

                    <div>
                      <div class="text-subtitle-2 font-weight-bold">Līga Vītola</div>
                      <div class="text-caption text-medium-emphasis">Vakar</div>
                    </div>
                  </div>

                  <p class="text-body-1 mb-3">
                    Meklēju kādu, kurš var iemainīt man "Mērnieks" pret citu latviešu klasiku.
                    Interesē arī jaunākas grāmatas!
                  </p>

                  <div class="d-flex gap-4">
                    <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">Patīk (12)</v-btn>
                    <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">Atbildēt (3)</v-btn>
                  </div>
                </v-card>
              </v-window-item>

              <!-- EXCHANGE TAB -->
              <v-window-item value="exchange">
                <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
                  <v-row align="center">
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model="bookSearch"
                        placeholder="Meklēt grāmatas..."
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        density="compact"
                        hide-details
                        class="search-field"
                      />
                    </v-col>

                    <v-col cols="6" sm="3">
                      <v-select
                        v-model="genreFilter"
                        :items="genres"
                        label="Žanrs"
                        variant="outlined"
                        density="compact"
                        hide-details
                      />
                    </v-col>

                    <v-col cols="6" sm="3">
                      <v-select
                        v-model="conditionFilter"
                        :items="conditions"
                        label="Stāvoklis"
                        variant="outlined"
                        density="compact"
                        hide-details
                      />
                    </v-col>
                  </v-row>
                </v-card>

                <div v-if="loadingAvailableBooks" class="text-center py-10">
                  <v-progress-circular indeterminate color="primary" />
                </div>

                <div v-else-if="paginatedExchangeBooks.length === 0" class="text-center py-10">
                  <v-icon size="56" color="grey">mdi-book-search</v-icon>
                  <div class="text-h6 mt-3">Nav pieejamu grāmatu</div>
                </div>

                <v-row v-else>
                  <v-col
                    v-for="book in paginatedExchangeBooks"
                    :key="book.id"
                    cols="12"
                    sm="6"
                  >
                    <v-card rounded="xl" elevation="2" class="pa-4 content-card">
                      <div class="d-flex">
                        <v-img
                          v-if="book.image"
                          :src="book.image"
                          width="100"
                          height="150"
                          class="rounded-lg mr-4"
                          cover
                        />

                        <div
                          v-else
                          class="rounded-lg mr-4 d-flex align-center justify-center"
                          style="width: 100px; height: 150px; background: rgba(var(--v-theme-on-surface), 0.06);"
                        >
                          <v-icon size="40">mdi-book-open-page-variant</v-icon>
                        </div>

                        <div class="flex-grow-1">
                          <div class="text-subtitle-1 font-weight-bold">{{ book.title }}</div>
                          <div class="text-body-2 text-medium-emphasis mb-2">{{ book.author }}</div>

                          <v-chip v-if="book.genre" size="x-small" color="primary" variant="tonal" class="mr-1">
                            {{ book.genre }}
                          </v-chip>

                          <v-chip v-if="book.condition" size="x-small" :color="getConditionColor(book.condition)" variant="tonal">
                            {{ book.condition }}
                          </v-chip>

                          <div class="text-caption text-medium-emphasis mt-2">
                            Īpašnieks: {{ book.user?.name }} {{ book.user?.surname }}
                          </div>
                        </div>
                      </div>

                      <v-divider class="my-3" />

                      <div class="d-flex justify-space-between align-center">
                        <span class="text-caption">@{{ book.user?.username }}</span>
                        <v-btn size="small" color="primary" variant="tonal">Apmainīties</v-btn>
                      </div>
                    </v-card>
                  </v-col>
                </v-row>

                <div v-if="exchangePageCount > 1" class="d-flex justify-center mt-6">
                  <v-pagination
                    v-model="exchangePage"
                    :length="exchangePageCount"
                    total-visible="5"
                    rounded="circle"
                  />
                </div>
              </v-window-item>
            </v-window>
          </v-col>

          <!-- Right Sidebar -->
          <v-col cols="12" md="3" class="d-none d-md-block">
            <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Populāras šonedēļ</div>

              <div v-for="item in trendingBooks" :key="item.title" class="d-flex align-center mb-3">
                <v-img
                  :src="item.image"
                  width="50"
                  height="70"
                  class="rounded-lg mr-3"
                  cover
                />

                <div>
                  <div class="text-body-2 font-weight-bold">{{ item.title }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.searches }} meklējumi</div>
                </div>
              </div>
            </v-card>

            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Ieteiktie draugi</div>

              <div
                v-for="friend in suggestedFriends"
                :key="friend.name"
                class="d-flex align-center justify-space-between mb-3"
              >
                <div class="d-flex align-center">
                  <v-avatar size="36" :color="friend.color" class="mr-3">
                    <span class="text-white">{{ friend.initials }}</span>
                  </v-avatar>

                  <div>
                    <div class="text-body-2 font-weight-bold">{{ friend.name }}</div>
                    <div class="text-caption text-medium-emphasis">{{ friend.mutual }} kopīgi draugi</div>
                  </div>
                </div>

                <v-btn size="small" variant="text" color="primary">Pievienot</v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
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
import { ref, computed, onMounted, watch } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"
import { userState, initials, logout } from "@/stores/userStore"

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

const showProfileReminder = ref(false)

onMounted(() => {
  fetchAvailableBooks()

  const alreadyShown = localStorage.getItem("profile_reminder_shown")
  if (!alreadyShown) {
    const user = userState.user

    if (!user?.city || !user?.about || !user?.avatar) {
      showProfileReminder.value = true
      localStorage.setItem("profile_reminder_shown", "true")
    }
  }
})

const activeTab = ref("feed")
const newPost = ref("")

const bookSearch = ref("")
const genreFilter = ref("Visi")
const conditionFilter = ref("Visi")

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
const exchangePage = ref(1)
const booksPerPage = 20

async function fetchAvailableBooks() {
  loadingAvailableBooks.value = true

  try {
    const response = await axios.get(`${API_URL}/books/available`, {
      headers: authHeaders(),
    })

    availableBooks.value = response.data
  } catch (error) {
    console.error(error)
  } finally {
    loadingAvailableBooks.value = false
  }
}

const filteredExchangeBooks = computed(() => {
  let result = [...availableBooks.value]

  const search = bookSearch.value.trim().toLowerCase()

  if (search) {
    result = result.filter((book) => {
      return (
        book.title?.toLowerCase().includes(search) ||
        book.author?.toLowerCase().includes(search) ||
        book.genre?.toLowerCase().includes(search)
      )
    })
  }

  if (genreFilter.value !== "Visi") {
    result = result.filter((book) => book.genre === genreFilter.value)
  }

  if (conditionFilter.value !== "Visi") {
    result = result.filter((book) => book.condition === conditionFilter.value)
  }

  return result
})

const exchangePageCount = computed(() => {
  return Math.ceil(filteredExchangeBooks.value.length / booksPerPage)
})

const paginatedExchangeBooks = computed(() => {
  const start = (exchangePage.value - 1) * booksPerPage
  return filteredExchangeBooks.value.slice(start, start + booksPerPage)
})

watch([bookSearch, genreFilter, conditionFilter], () => {
  exchangePage.value = 1
})

function getConditionColor(condition) {
  const colors = {
    Jauna: "success",
    "Labā stāvoklī": "primary",
    "Vidējā stāvoklī": "warning",
    "Sliktā stāvoklī": "error",
  }

  return colors[condition] || "grey"
}

const trendingBooks = [
  {
    title: "Staburadis",
    searches: 12,
    image: "https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=80",
  },
  {
    title: "1984",
    searches: 8,
    image: "https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=80",
  },
]

const suggestedFriends = [
  { name: "Māris R.", initials: "MR", mutual: 8, color: "pink" },
  { name: "Elīna L.", initials: "EL", mutual: 5, color: "indigo" },
]
</script>

<style scoped>
.main-page {
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

.search-field :deep(.v-field) {
  background: rgba(var(--v-theme-on-surface), 0.05);
}

.gap-2 {
  gap: 8px;
}

.gap-4 {
  gap: 16px;
}
</style>