<template>
  <div class="profile-page">
    <AppHeader :user="currentUser" :initials="currentInitials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6" style="max-width: 1200px;">
        <v-row>
          <v-col cols="12" md="3" class="d-none d-md-block">
            <AppSidebar :user="currentUser" :initials="currentInitials" @logout="logoutUser" />
          </v-col>

          <v-col cols="12" md="6">
            <div v-if="loading" class="text-center py-10">
              <v-progress-circular indeterminate color="primary" />
            </div>

            <template v-else>
              <v-card rounded="xl" elevation="2" class="profile-hero mb-4">
                <div class="cover"></div>

                <div class="pa-6">
                    <div class="d-flex align-end justify-space-between profile-header">

                    <!-- LEFT SIDE (avatar + user info) -->
                    <div class="d-flex align-end">
                        <v-avatar size="104" color="primary" class="profile-avatar mr-4">
                        <v-img v-if="profileUser?.avatar" :src="profileUser.avatar" />
                        <span v-else class="text-h3 text-white">{{ profileInitials }}</span>
                        </v-avatar>

                        <div>
                        <div class="text-h5 font-weight-bold">
                            {{ profileUser?.name }} {{ profileUser?.surname }}
                        </div>

                        <div class="text-body-2 text-medium-emphasis">
                            @{{ profileUser?.username }}
                        </div>

                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <v-chip color="primary" variant="tonal" size="small">
                            Lasītājs
                            </v-chip>

                            <v-chip
                            v-if="profileUser?.city"
                            color="success"
                            variant="tonal"
                            size="small"
                            >
                            {{ profileUser.city }}
                            </v-chip>
                        </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE (button) -->
                    <v-btn
                        color="primary"
                        variant="tonal"
                        prepend-icon="mdi-message-text"
                        @click="openChat"
                    >
                        Rakstīt
                    </v-btn>

                    </div>
                </div>
                </v-card>

              <v-card rounded="xl" elevation="0" class="pa-5 mb-4 content-card">
                <div class="text-subtitle-1 font-weight-bold mb-2">Par mani</div>

                <p v-if="profileUser?.about" class="text-body-1 mb-0">
                  {{ profileUser.about }}
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
                      Grāmatas, kuras lietotājs piedāvā apmaiņai
                    </div>
                  </div>
                </div>

                <v-row v-if="books.length">
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
                        class="rounded-lg mb-2 d-flex align-center justify-center"
                        style="height: 220px; background: rgba(var(--v-theme-on-surface), 0.06);"
                      >
                        <v-icon size="44">mdi-book-open-page-variant</v-icon>
                      </div>

                      <div class="text-subtitle-2 font-weight-bold">{{ book.title }}</div>
                      <div class="text-caption text-medium-emphasis">{{ book.author }}</div>
                    </v-card>
                  </v-col>
                </v-row>

                <div v-else class="text-body-2 text-medium-emphasis">
                  Lietotājam vēl nav publisku grāmatu.
                </div>
              </v-card>
            </template>
          </v-col>

          <v-col cols="12" md="3" class="d-none d-md-block">
            <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Profila informācija</div>

              <div class="text-body-2 mb-2">
                <strong>Pilsēta:</strong>
                {{ profileUser?.city || "Nav norādīta" }}
              </div>

              <div class="text-body-2">
                <strong>Grāmatas:</strong>
                {{ books.length }}
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"

const route = useRoute()
const router = useRouter()

const API_URL = "http://127.0.0.1:8000/api"

const currentUser = ref(JSON.parse(localStorage.getItem("user")) || null)
const profileUser = ref(null)
const books = ref([])
const loading = ref(false)

const currentInitials = computed(() => getInitials(currentUser.value))
const profileInitials = computed(() => getInitials(profileUser.value))

function getInitials(user) {
  return `${user?.name?.[0] || ""}${user?.surname?.[0] || ""}`.toUpperCase() || "U"
}

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

onMounted(() => {
  fetchUserProfile()
})

watch(
  () => route.params.id,
  () => {
    fetchUserProfile()
  }
)

async function fetchUserProfile() {
  loading.value = true

  try {
    const response = await axios.get(`${API_URL}/users/${route.params.id}`, {
      headers: authHeaders(),
    })

    profileUser.value = response.data.user
    books.value = response.data.books
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

async function openChat() {
  if (!profileUser.value) return

  try {
    const response = await axios.get(
      `${API_URL}/conversations/user/${profileUser.value.id}`,
      {
        headers: authHeaders(),
      }
    )

    router.push({
      path: "/messages",
      query: { conversation: response.data.id },
    })
  } catch (error) {
    console.error(error)
  }
}
</script>

<style scoped>
.profile-page {
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
</style>