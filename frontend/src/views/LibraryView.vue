<template>
  <div ref="libraryPage" class="locked-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 locked-page__content">
      <v-container class="py-6 locked-page__shell" style="max-width: 1200px;">
        <v-row class="locked-page__row">
          <v-col cols="12" md="3" class="d-none d-md-block locked-page__side">
            <AppSidebar :user="user" :initials="initials" @logout="logoutUser" />
          </v-col>

          <v-col ref="libraryScroll" cols="12" md="6" class="locked-page__scroll">
            <LibraryBooksSection
              :books="books"
              :loading="loadingBooks"
              @add="openAddDialog"
              @edit="openEditDialog"
              @delete="deleteBook"
            />
          </v-col>

          <LibraryActivitySidebar class="locked-page__side" :books="books" />
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
import { computed, onMounted, ref } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"
import { API_URL } from "@/services/api"
import { usePageLock } from "@/composables/usePageLock"
import { useScrollForwarding } from "@/composables/useScrollForwarding"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"
import BookFormDialog from "@/components/library/BookFormDialog.vue"
import LibraryActivitySidebar from "@/components/library/LibraryActivitySidebar.vue"
import LibraryBooksSection from "@/components/library/LibraryBooksSection.vue"

const router = useRouter()
const libraryPage = ref(null)
const libraryScroll = ref(null)

usePageLock()

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
