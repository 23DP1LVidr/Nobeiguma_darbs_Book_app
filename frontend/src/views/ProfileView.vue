<template>
  <div class="locked-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 locked-page__content">
      <v-container class="py-6 locked-page__shell" style="max-width: 1200px;">
        <v-row class="locked-page__row">
          <v-col cols="12" md="3" class="d-none d-md-block locked-page__side">
            <AppSidebar :user="user" :initials="initials" @logout="logoutUser" />
          </v-col>

          <v-col cols="12" md="6" class="locked-page__scroll">
            <v-card rounded="xl" elevation="2" class="profile-hero mb-4">
              <div class="cover"></div>

              <div class="pa-6">
                <div class="d-flex align-end justify-space-between profile-header">
                  <div class="d-flex align-end">
                    <v-avatar size="104" color="primary" class="profile-avatar mr-4">
                      <v-img v-if="user?.avatar" :src="user.avatar" />
                      <span v-else class="text-h3 text-white">{{ initials }}</span>
                    </v-avatar>

                    <div>
                      <div class="text-h5 font-weight-bold">
                        {{ user?.name }} {{ user?.surname }}
                      </div>

                      <div class="text-body-2 text-medium-emphasis">
                        @{{ user?.username }}
                      </div>

                      <div class="d-flex gap-2 mt-2 flex-wrap">
                        <v-chip color="primary" variant="tonal" size="small">
                          Lasītājs
                        </v-chip>

                        <v-chip
                          v-if="user?.city"
                          color="success"
                          variant="tonal"
                          size="small"
                        >
                          {{ user.city }}
                        </v-chip>
                      </div>
                    </div>
                  </div>

                  <v-btn
                    color="primary"
                    variant="tonal"
                    prepend-icon="mdi-pencil"
                    @click="openEditDialog"
                  >
                    Rediģēt
                  </v-btn>
                </div>
              </div>
            </v-card>

            <v-card rounded="xl" elevation="0" class="pa-5 mb-4 content-card">
              <div class="text-subtitle-1 font-weight-bold mb-2">Par mani</div>

              <p v-if="user?.about" class="text-body-1 mb-0">
                {{ user.about }}
              </p>

              <p v-else class="text-body-1 text-medium-emphasis mb-0">
                Lietotājs vēl nav pievienojis aprakstu.
              </p>
            </v-card>

            <v-card rounded="xl" elevation="0" class="pa-5 mb-4 content-card">
              <div class="d-flex justify-space-between align-center mb-4">
                <div>
                  <div class="text-subtitle-1 font-weight-bold">Publiskās grāmatas</div>
                  <div class="text-caption text-medium-emphasis">
                    Grāmatas, kuras redz citi lietotāji
                  </div>
                </div>

                <v-btn variant="text" color="primary" @click="router.push('/library')">
                  Skatīt visas
                </v-btn>
              </div>

              <div v-if="loadingBooks" class="text-center py-6">
                <v-progress-circular indeterminate color="primary" size="28" />
              </div>

              <v-row v-else-if="books.length">
                <v-col cols="12" sm="6" md="4" v-for="book in books" :key="book.id">
                  <v-card rounded="xl" elevation="1" class="pa-3 book-card">
                    <v-img
                      v-if="book.image"
                      :src="book.image"
                      aspect-ratio="0.7"
                      class="rounded-lg mb-2"
                      cover
                    />

                    <div
                      v-else
                      class="rounded-lg mb-2 d-flex align-center justify-center book-placeholder"
                    >
                      <v-icon size="44">mdi-book-open-page-variant</v-icon>
                    </div>

                    <div class="text-subtitle-2 font-weight-bold">{{ book.title }}</div>
                    <div class="text-caption text-medium-emphasis">{{ book.author }}</div>
                  </v-card>
                </v-col>
              </v-row>

              <div v-else class="text-body-2 text-medium-emphasis">
                Tev vēl nav pievienotu grāmatu.
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="3" class="d-none d-md-block locked-page__side">
            <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Mīļākie žanri</div>

              <div v-if="topGenres.length" class="d-flex gap-2 flex-wrap">
                <v-chip
                  v-for="genre in topGenres"
                  :key="genre.name"
                  color="primary"
                  variant="tonal"
                >
                  {{ genre.name }} ({{ genre.count }})
                </v-chip>
              </div>

              <div v-else class="text-body-2 text-medium-emphasis">
                Žanri parādīsies, kad pievienosi grāmatas.
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>

    <v-dialog v-model="showEditDialog" max-width="520">
      <v-card rounded="xl" class="pa-6 content-card">
        <div class="text-h6 font-weight-bold mb-4">Rediģēt profilu</div>

        <v-file-input
          v-model="editForm.avatarFile"
          label="Profila attēls"
          accept="image/*"
          prepend-icon="mdi-image"
          show-size
          class="mb-4"
        />

        <v-avatar size="80" class="mb-4" v-if="avatarPreview">
          <v-img :src="avatarPreview" />
        </v-avatar>

        <v-text-field v-model="editForm.name" label="Vārds" variant="outlined" class="mb-3" />
        <v-text-field v-model="editForm.surname" label="Uzvārds" variant="outlined" class="mb-3" />
        <v-text-field v-model="editForm.city" label="Pilsēta" variant="outlined" class="mb-3" />

        <v-textarea
          v-model="editForm.about"
          label="Par mani"
          variant="outlined"
          rows="4"
          auto-grow
        />

        <div class="d-flex justify-end mt-4">
          <v-btn variant="text" class="mr-2" @click="showEditDialog = false">
            Atcelt
          </v-btn>

          <v-btn color="primary" :loading="saving" @click="saveProfile">
            Saglabāt
          </v-btn>
        </div>
      </v-card>
    </v-dialog>
    <v-snackbar
      v-model="showSnackbar"
      :color="snackbarColor"
      timeout="3000"
    >
      {{ snackbarMessage }}

      <template #actions>
        <v-btn variant="text" @click="showSnackbar = false">
          Aizvērt
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue"
import { useRouter } from "vue-router"
import axios from "axios"
import { API_URL } from "@/services/api"
import { usePageLock } from "@/composables/usePageLock"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")

const router = useRouter()

usePageLock()

const user = ref(JSON.parse(localStorage.getItem("user")) || null)
const books = ref([])
const loadingBooks = ref(false)
const showEditDialog = ref(false)
const saving = ref(false)
const avatarPreview = ref(user.value?.avatar || "")

const editForm = reactive({
  name: "",
  surname: "",
  city: "",
  about: "",
  avatarFile: null,
})

const initials = computed(() => {
  const nameInitial = user.value?.name?.[0] || ""
  const surnameInitial = user.value?.surname?.[0] || ""
  return `${nameInitial}${surnameInitial}`.toUpperCase() || "U"
})

const topGenres = computed(() => {
  const counts = books.value.reduce((result, book) => {
    if (!book.genre) return result
    result[book.genre] = (result[book.genre] || 0) + 1
    return result
  }, {})

  return Object.entries(counts)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count || a.name.localeCompare(b.name))
    .slice(0, 4)
})

onMounted(() => {
  fetchBooks()
})

watch(
  () => editForm.avatarFile,
  (file) => {
    if (!file) return

    const selected = Array.isArray(file) ? file[0] : file
    if (!selected) return

    const reader = new FileReader()
    reader.onload = (e) => {
      avatarPreview.value = e.target.result
    }
    reader.readAsDataURL(selected)
  }
)

function openEditDialog() {
  editForm.name = user.value?.name || ""
  editForm.surname = user.value?.surname || ""
  editForm.city = user.value?.city || ""
  editForm.about = user.value?.about || ""
  editForm.avatarFile = null
  avatarPreview.value = user.value?.avatar || ""
  showEditDialog.value = true
}

async function saveProfile() {
  saving.value = true

  try {
    const token = localStorage.getItem("token")

    const response = await axios.put(
      `${API_URL}/profile`,
      {
        name: editForm.name,
        surname: editForm.surname,
        city: editForm.city,
        about: editForm.about,
        avatar: avatarPreview.value,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )

    user.value = response.data.user
    localStorage.setItem("user", JSON.stringify(response.data.user))
    showEditDialog.value = false

    snackbarMessage.value = "Profils veiksmīgi saglabāts."
    snackbarColor.value = "success"
    showSnackbar.value = true
    } catch (error) {
      console.error(error)

      snackbarMessage.value = "Neizdevās saglabāt profilu."
      snackbarColor.value = "error"
      showSnackbar.value = true
    } finally {
      saving.value = false
    }
}

async function fetchBooks() {
  const token = localStorage.getItem("token")

  if (!token) {
    router.replace("/register")
    return
  }

  loadingBooks.value = true

  try {
    const response = await axios.get(`${API_URL}/books`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    books.value = response.data
  } catch (error) {
    console.error(error)
  } finally {
    loadingBooks.value = false
  }
}

function logoutUser() {
  localStorage.removeItem("token")
  localStorage.removeItem("user")
  router.replace("/login")
}
</script>

<style scoped>
.panel-card,
.content-card,
.book-card,
.profile-hero {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.cover {
  height: 180px;
  border-radius: 24px 24px 0 0;
  background:
    linear-gradient(135deg, rgba(var(--v-theme-primary), 0.45), rgba(var(--v-theme-primary), 0.1)),
    url("https://images.unsplash.com/photo-1519682337058-a94d519337bc?w=1200") center/cover;
}

.profile-avatar {
  border: 4px solid rgb(var(--v-theme-surface));
  margin-top: -52px;
}

.profile-header {
  gap: 16px;
}

.gap-2 {
  gap: 8px;
}

.book-placeholder {
  height: 220px;
  background: rgba(var(--v-theme-on-surface), 0.06);
}

</style>
