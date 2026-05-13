<template>
  <div ref="libraryPage" class="library-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6 library-shell" style="max-width: 1200px;">
        <v-row class="library-layout-row">
          <v-col cols="12" md="3" class="d-none d-md-block sidebar-col">
            <AppSidebar :user="user" :initials="initials" @logout="logoutUser" />
          </v-col>

          <v-col ref="libraryScroll" cols="12" md="6" class="library-scroll-col">
            <LibraryBooksSection
              :books="books"
              :loading="loadingBooks"
              @add="openAddDialog"
              @edit="openEditDialog"
              @delete="deleteBook"
            />
          </v-col>

          <LibraryActivitySidebar class="sidebar-col" :books="books" />
        </v-row>
      </v-container>
    </v-container>

    <BookFormDialog
      v-model="showAddBookDialog"
      mode="add"
      :genres="genres"
      :conditions="conditions"
      :saving="savingBook"
      @save="addBook"
    />

    <BookFormDialog
      v-model="showEditDialog"
      mode="edit"
      :book="selectedBook"
      :genres="genres"
      :conditions="conditions"
      :saving="savingEdit"
      @save="updateBook"
    />

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
import { useScrollForwarding } from "@/composables/useScrollForwarding"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"
import BookFormDialog from "@/components/library/BookFormDialog.vue"
import LibraryActivitySidebar from "@/components/library/LibraryActivitySidebar.vue"
import LibraryBooksSection from "@/components/library/LibraryBooksSection.vue"

const router = useRouter()
const libraryPage = ref(null)
const libraryScroll = ref(null)

useScrollForwarding({
  source: libraryPage,
  target: libraryScroll,
})

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

function authHeaders() {
  return {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  }
}

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

const books = ref([])
const loadingBooks = ref(false)

const showAddBookDialog = ref(false)
const savingBook = ref(false)

const showEditDialog = ref(false)
const savingEdit = ref(false)
const selectedBook = ref(null)

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")

onMounted(() => {
  document.documentElement.classList.add("library-page-locked")
  document.body.classList.add("library-page-locked")

  fetchBooks()
})

onBeforeUnmount(() => {
  document.documentElement.classList.remove("library-page-locked")
  document.body.classList.remove("library-page-locked")
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

function openAddDialog() {
  showAddBookDialog.value = true
}

function openEditDialog(book) {
  selectedBook.value = book
  showEditDialog.value = true
}

async function addBook(book) {
  if (!book.title || !book.author) {
    showMessage("Nosaukums un autors ir obligāti.", "error")
    return
  }

  savingBook.value = true

  try {
    await axios.post(
      `${API_URL}/books`,
      bookPayload(book),
      {
        headers: authHeaders(),
      }
    )

    showAddBookDialog.value = false
    await fetchBooks()
    showMessage("Grāmata pievienota.", "success")
  } catch (error) {
    console.error("Book save error:", error.response?.data || error)
    showMessage(error.response?.data?.message || "Neizdevās saglabāt grāmatu.", "error")
  } finally {
    savingBook.value = false
  }
}

async function updateBook(book) {
  if (!book.title || !book.author) {
    showMessage("Nosaukums un autors ir obligāti.", "error")
    return
  }

  savingEdit.value = true

  try {
    await axios.put(
      `${API_URL}/books/${book.id}`,
      bookPayload(book),
      {
        headers: authHeaders(),
      }
    )

    showEditDialog.value = false
    selectedBook.value = null
    await fetchBooks()
    showMessage("Grāmata atjaunināta.", "success")
  } catch (error) {
    console.error(error)
    showMessage("Neizdevās atjaunināt grāmatu.", "error")
  } finally {
    savingEdit.value = false
  }
}

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

function bookPayload(book) {
  return {
    title: book.title,
    author: book.author,
    genre: book.genre,
    condition: book.condition,
    image: book.image,
  }
}

function showMessage(message, color = "success") {
  snackbarMessage.value = message
  snackbarColor.value = color
  showSnackbar.value = true
}
</script>

<style scoped>
:global(html.library-page-locked),
:global(body.library-page-locked) {
  height: 100%;
  overflow: hidden !important;
}

:global(body.library-page-locked #app),
:global(body.library-page-locked .v-application),
:global(body.library-page-locked .v-application__wrap),
:global(body.library-page-locked .v-main) {
  height: 100%;
  min-height: 0;
  overflow: hidden;
}

.library-page {
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

.library-shell {
  height: 100%;
  box-sizing: border-box;
}

.library-layout-row {
  height: 100%;
  min-height: 0;
}

.library-scroll-col {
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
  :global(html.library-page-locked),
  :global(body.library-page-locked) {
    height: auto;
    overflow: visible !important;
  }

  .library-page,
  .page-content {
    height: auto;
    min-height: 100vh;
    overflow: visible;
  }

  .library-scroll-col {
    height: auto;
    overflow: visible;
  }
}
</style>
