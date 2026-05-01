<template>
  <div class="library-page">
    <v-app-bar elevation="1" class="px-4 app-header">
      <div class="d-flex align-center" style="width: 280px;">
        <router-link to="/main" class="text-decoration-none">
          <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
        </router-link>
      </div>

      <v-spacer />

      <router-link to="/profile" class="text-decoration-none">
        <v-avatar size="36" color="primary" class="ml-2" style="cursor:pointer;">
          <v-img v-if="user?.avatar" :src="user.avatar" />
          <span v-else class="text-white text-caption">{{ initials }}</span>
        </v-avatar>
      </router-link>
    </v-app-bar>

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6" style="max-width: 1200px;">
        <v-row>
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
          </v-col>

          <v-col cols="12" md="6">
            <div class="d-flex justify-space-between align-center mb-6">
              <div>
                <h1 class="text-h4 font-weight-bold">Mana bibliotēka</h1>
                <p class="text-body-1 text-medium-emphasis">
                  Jūsu personīgais grāmatu krājums
                </p>
              </div>

              <v-btn color="primary" prepend-icon="mdi-plus" @click="showAddBookDialog = true">
                Pievienot grāmatu
              </v-btn>
            </div>

            <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
              <v-text-field
                v-model="bookSearch"
                placeholder="Meklēt pēc nosaukuma vai autora..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                class="mb-4"
              />

              <v-row>
                <v-col cols="12" sm="8">
                  <v-select
                    v-model="sortMode"
                    :items="sortOptions"
                    label="Kārtot pēc"
                    variant="outlined"
                    density="compact"
                    hide-details
                  />
                </v-col>

                <v-col cols="12" sm="4">
                  <v-btn
                    block
                    variant="outlined"
                    height="40"
                    :prepend-icon="sortDirection === 'asc' ? 'mdi-sort-ascending' : 'mdi-sort-descending'"
                    @click="toggleSortDirection"
                  >
                    {{ sortDirection === "asc" ? "A-Z" : "Z-A" }}
                  </v-btn>
                </v-col>
              </v-row>
            </v-card>

            <div v-if="loadingBooks" class="text-center py-10">
              <v-progress-circular indeterminate color="primary" />
            </div>

            <div v-else-if="filteredBooks.length === 0" class="text-center py-10">
              <v-icon size="56" color="grey">mdi-book-open-page-variant</v-icon>
              <div class="text-h6 mt-3">Nav atrastu grāmatu</div>
              <div class="text-body-2 text-medium-emphasis mb-4">
                Pievieno grāmatu vai maini meklēšanas/filtra iestatījumus.
              </div>
              <v-btn color="primary" prepend-icon="mdi-plus" @click="showAddBookDialog = true">
                Pievienot grāmatu
              </v-btn>
            </div>

            <v-row v-else>
              <v-col
                v-for="book in filteredBooks"
                :key="book.id"
                cols="12"
                sm="6"
                md="4"
              >
                <v-card rounded="xl" elevation="2" class="pa-4 h-100 book-card">
                  <div class="d-flex flex-column h-100">
                    <v-img
                      v-if="book.image"
                      :src="book.image"
                      aspect-ratio="0.7"
                      class="rounded-lg mb-3"
                      cover
                    />

                    <div
                      v-else
                      class="rounded-lg mb-3 d-flex align-center justify-center"
                      style="height: 220px; background: rgba(var(--v-theme-on-surface), 0.06);"
                    >
                      <v-icon size="48">mdi-book-open-page-variant</v-icon>
                    </div>

                    <div class="flex-grow-1">
                      <div class="text-subtitle-1 font-weight-bold mb-1">
                        {{ book.title }}
                      </div>

                      <div class="text-body-2 text-medium-emphasis mb-2">
                        {{ book.author }}
                      </div>

                      <div class="d-flex gap-2 mb-2 flex-wrap">
                        <v-chip v-if="book.genre" size="x-small" color="primary" variant="tonal">
                          {{ book.genre }}
                        </v-chip>

                        <v-chip
                          v-if="book.condition"
                          size="x-small"
                          :color="getConditionColor(book.condition)"
                          variant="tonal"
                        >
                          {{ book.condition }}
                        </v-chip>
                      </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-auto">
                      <v-btn size="small" variant="text" prepend-icon="mdi-pencil" @click="openEditDialog(book)">
                        Rediģēt
                      </v-btn>

                      <v-btn
                        size="small"
                        variant="text"
                        color="error"
                        prepend-icon="mdi-delete"
                        @click="deleteBook(book.id)"
                      >
                        Dzēst
                      </v-btn>
                    </div>
                  </div>
                </v-card>
              </v-col>

              <v-col cols="12" sm="6" md="4">
                <v-card
                  rounded="xl"
                  elevation="0"
                  class="pa-4 h-100 d-flex align-center justify-center add-card"
                  @click="showAddBookDialog = true"
                >
                  <div class="text-center">
                    <v-icon size="48" color="grey">mdi-plus</v-icon>
                    <div class="text-body-1 text-medium-emphasis mt-2">
                      Pievienot grāmatu
                    </div>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-col>

          <v-col cols="12" md="3" class="d-none d-md-block">
            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Nesenā aktivitāte</div>

              <v-timeline density="compact" side="end">
                <v-timeline-item dot-color="primary" size="x-small">
                  <div class="text-body-2">Bibliotēka atjaunināta</div>
                  <div class="text-caption text-medium-emphasis">Nesen</div>
                </v-timeline-item>
              </v-timeline>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>

    <v-dialog v-model="showAddBookDialog" max-width="520">
      <v-card rounded="xl" class="pa-6 content-card">
        <div class="text-h6 font-weight-bold mb-4">Pievienot grāmatu</div>

        <v-text-field v-model="newBook.title" label="Nosaukums" variant="outlined" class="mb-3" />
        <v-text-field v-model="newBook.author" label="Autors" variant="outlined" class="mb-3" />

        <v-select v-model="newBook.genre" :items="genres" label="Žanrs" variant="outlined" class="mb-3" />
        <v-select v-model="newBook.condition" :items="conditions" label="Stāvoklis" variant="outlined" class="mb-3" />

        <v-file-input
          v-model="newBook.imageFile"
          label="Grāmatas attēls"
          accept="image/*"
          prepend-icon="mdi-image"
          show-size
          class="mb-3"
        />

        <v-img v-if="imagePreview" :src="imagePreview" height="160" class="rounded mb-3" cover />

        <div class="d-flex justify-end mt-4">
          <v-btn variant="text" class="mr-2" @click="closeAddBookDialog">
            Atcelt
          </v-btn>

          <v-btn color="primary" :loading="savingBook" @click="addBook">
            Saglabāt
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="showEditDialog" max-width="520">
      <v-card rounded="xl" class="pa-6 content-card">
        <div class="text-h6 font-weight-bold mb-4">Rediģēt grāmatu</div>

        <v-text-field v-model="editBook.title" label="Nosaukums" variant="outlined" class="mb-3" />
        <v-text-field v-model="editBook.author" label="Autors" variant="outlined" class="mb-3" />

        <v-select v-model="editBook.genre" :items="genres" label="Žanrs" variant="outlined" class="mb-3" />
        <v-select v-model="editBook.condition" :items="conditions" label="Stāvoklis" variant="outlined" class="mb-3" />

        <v-file-input
          v-model="editBook.imageFile"
          label="Grāmatas attēls"
          accept="image/*"
          prepend-icon="mdi-image"
          show-size
          class="mb-3"
        />

        <v-img v-if="editImagePreview" :src="editImagePreview" height="160" class="rounded mb-3" cover />

        <div class="d-flex justify-end mt-4">
          <v-btn variant="text" class="mr-2" @click="showEditDialog = false">
            Atcelt
          </v-btn>

          <v-btn color="primary" :loading="savingEdit" @click="updateBook">
            Saglabāt
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="showSnackbar" :color="snackbarColor" timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"

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

const books = ref([])
const loadingBooks = ref(false)

const bookSearch = ref("")
const sortMode = ref("alphabetic")
const sortDirection = ref("asc")
const conditionFilter = ref("all")

const sortOptions = [
  { title: "Alfabētiski", value: "alphabetic" },
  { title: "Pēc stāvokļa", value: "condition" },
]

function toggleSortDirection() {
  sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc"
}

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")

const genres = [
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
  "Jauna",
  "Labā stāvoklī",
  "Vidējā stāvoklī",
  "Sliktā stāvoklī",
]

const showAddBookDialog = ref(false)
const savingBook = ref(false)
const imagePreview = ref(null)

const newBook = ref({
  title: "",
  author: "",
  genre: "",
  condition: "",
  image: "",
  imageFile: null,
})

const showEditDialog = ref(false)
const savingEdit = ref(false)
const editImagePreview = ref(null)

const editBook = ref({
  id: null,
  title: "",
  author: "",
  genre: "",
  condition: "",
  image: "",
  imageFile: null,
})

const conditionOrder = {
  "Jauna": 1,
  "Labā stāvoklī": 2,
  "Vidējā stāvoklī": 3,
  "Sliktā stāvoklī": 4,
}

const filteredBooks = computed(() => {
  let result = [...books.value]

  const search = bookSearch.value.trim().toLowerCase()

  if (search) {
    result = result.filter((book) => {
      return (
        book.title?.toLowerCase().includes(search) ||
        book.author?.toLowerCase().includes(search)
      )
    })
  }

  result.sort((a, b) => {
    let comparison = 0

    if (sortMode.value === "alphabetic") {
      comparison = (a.title || "").localeCompare(b.title || "", "lv")
    }

    if (sortMode.value === "condition") {
      comparison =
        (conditionOrder[a.condition] || 999) -
        (conditionOrder[b.condition] || 999)
    }

    return sortDirection.value === "asc" ? comparison : -comparison
  })

  return result
})

onMounted(() => {
  fetchBooks()
})

async function fetchBooks() {
  loadingBooks.value = true

  try {
    const response = await axios.get(`${API_URL}/books`, {
      headers: authHeaders(),
    })

    books.value = response.data
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās ielādēt grāmatas.", "error")
  } finally {
    loadingBooks.value = false
  }
}

async function addBook() {
  if (!newBook.value.title || !newBook.value.author) {
    showMessage("Nosaukums un autors ir obligāti.", "error")
    return
  }

  savingBook.value = true

  try {
    await axios.post(
      `${API_URL}/books`,
      {
        title: newBook.value.title,
        author: newBook.value.author,
        genre: newBook.value.genre,
        condition: newBook.value.condition,
        image: newBook.value.image,
      },
      {
        headers: authHeaders(),
      }
    )

    closeAddBookDialog()
    await fetchBooks()
    showMessage("Grāmata pievienota.", "success")
  } catch (error) {
    console.error("Book save error:", error.response?.data || error)
    showMessage(error.response?.data?.message || "Neizdevās saglabāt grāmatu.", "error")
  } finally {
    savingBook.value = false
  }
}

function closeAddBookDialog() {
  showAddBookDialog.value = false

  newBook.value = {
    title: "",
    author: "",
    genre: "",
    condition: "",
    image: "",
    imageFile: null,
  }

  imagePreview.value = null
}

watch(
  () => newBook.value.imageFile,
  (file) => {
    if (!file) return

    const selected = Array.isArray(file) ? file[0] : file
    if (!selected) return

    const reader = new FileReader()

    reader.onload = (e) => {
      imagePreview.value = e.target.result
      newBook.value.image = e.target.result
    }

    reader.readAsDataURL(selected)
  }
)

function openEditDialog(book) {
  editBook.value = {
    id: book.id,
    title: book.title,
    author: book.author,
    genre: book.genre,
    condition: book.condition,
    image: book.image,
    imageFile: null,
  }

  editImagePreview.value = book.image
  showEditDialog.value = true
}

async function updateBook() {
  if (!editBook.value.title || !editBook.value.author) {
    showMessage("Nosaukums un autors ir obligāti.", "error")
    return
  }

  savingEdit.value = true

  try {
    await axios.put(
      `${API_URL}/books/${editBook.value.id}`,
      {
        title: editBook.value.title,
        author: editBook.value.author,
        genre: editBook.value.genre,
        condition: editBook.value.condition,
        image: editBook.value.image,
      },
      {
        headers: authHeaders(),
      }
    )

    showEditDialog.value = false
    await fetchBooks()
    showMessage("Grāmata atjaunināta.", "success")
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās atjaunināt grāmatu.", "error")
  } finally {
    savingEdit.value = false
  }
}

watch(
  () => editBook.value.imageFile,
  (file) => {
    if (!file) return

    const selected = Array.isArray(file) ? file[0] : file
    if (!selected) return

    const reader = new FileReader()

    reader.onload = (e) => {
      editImagePreview.value = e.target.result
      editBook.value.image = e.target.result
    }

    reader.readAsDataURL(selected)
  }
)

async function deleteBook(bookId) {
  try {
    await axios.delete(`${API_URL}/books/${bookId}`, {
      headers: authHeaders(),
    })

    await fetchBooks()
    showMessage("Grāmata dzēsta.", "success")
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās dzēst grāmatu.", "error")
  }
}

function getConditionColor(condition) {
  const colors = {
    Jauna: "success",
    "Labā stāvoklī": "primary",
    "Vidējā stāvoklī": "warning",
    "Sliktā stāvoklī": "error",
  }

  return colors[condition] || "grey"
}

function showMessage(message, color = "success") {
  snackbarMessage.value = message
  snackbarColor.value = color
  showSnackbar.value = true
}
</script>

<style scoped>
.library-page {
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
.book-card,
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.add-card {
  min-height: 300px;
  cursor: pointer;
  background: transparent !important;
  border: 2px dashed rgba(var(--v-theme-on-surface), 0.35);
}

.gap-2 {
  gap: 8px;
}
</style>