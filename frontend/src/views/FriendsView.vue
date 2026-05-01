<template>
  <div class="friends-page">
    <!-- Top Bar -->
    <v-app-bar elevation="1" class="px-4 app-header">
      <div class="d-flex align-center" style="width: 280px;">
        <router-link to="/main" class="text-decoration-none">
          <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
        </router-link>
      </div>

      <v-spacer />

      <router-link to="/profile" class="text-decoration-none">
        <v-avatar size="36" color="primary">
          <v-img v-if="user?.avatar" :src="user.avatar" />
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

            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-2 font-weight-bold mb-3">Apmaiņu statistika</div>
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Kopā apmainīts</span>
                <span class="text-body-2 font-weight-bold">8</span>
              </div>
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Gaida apmaiņu</span>
                <span class="text-body-2 font-weight-bold">3</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-body-2">Jūsu piedāvājumi</span>
                <span class="text-body-2 font-weight-bold">2</span>
              </div>
            </v-card>
          </v-col>

          <!-- Main Content -->
          <v-col cols="12" md="6">
            <div class="d-flex justify-space-between align-center mb-6">
              <div>
                <h1 class="text-h4 font-weight-bold">Draugi</h1>
                <p class="text-body-1 text-medium-emphasis">Jūsu savienojumi BookSwap</p>
              </div>
            </div>

          <v-text-field
            v-model="search"
            placeholder="Meklēt draugus..."
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            hide-details
            class="mb-4 search-field"
          />
            
            <!-- Friends List -->
            <v-card
              v-for="friend in filteredFriends"
              :key="friend.id"
              class="pa-4 mb-4 content-card"
              rounded="xl"
              elevation="1"
            >
            
            
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-avatar size="48" :color="friend.color" class="mr-3">
                    <span class="text-white">{{ friend.initials }}</span>
                  </v-avatar>

                  <div>
                    <div class="text-subtitle-1 font-weight-bold">{{ friend.name }}</div>
                    <div class="text-caption text-medium-emphasis">
                      {{ friend.books }} grāmatas
                    </div>
                  </div>
                </div>

                <div>
                  <v-btn size="small" variant="text" prepend-icon="mdi-message">
                    Ziņa
                  </v-btn>

                  <v-btn size="small" color="error" variant="text">
                    Noņemt
                  </v-btn>
                </div>
              </div>
            </v-card>
          </v-col>

          <!-- Right Sidebar -->
          <v-col cols="12" md="3" class="d-none d-md-block">
            <v-card class="pa-4 panel-card" rounded="xl" elevation="0">
              <div class="text-subtitle-1 font-weight-bold mb-3">
                Ieteiktie draugi
              </div>

              <div
                v-for="user in suggestions"
                :key="user.id"
                class="d-flex align-center justify-space-between mb-3"
              >
                <div class="d-flex align-center">
                  <v-avatar size="36" :color="user.color" class="mr-3">
                    <span class="text-white">{{ user.initials }}</span>
                  </v-avatar>

                  <div>
                    <div class="text-body-2 font-weight-bold">{{ user.name }}</div>
                    <div class="text-caption text-medium-emphasis">
                      {{ user.mutual }} kopīgi draugi
                    </div>
                  </div>
                </div>

                <v-btn size="small" variant="text" color="primary">
                  Pievienot
                </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from "vue"
import { useRouter } from "vue-router"

const router = useRouter()

// User data from localStorage
const user = ref(JSON.parse(localStorage.getItem("user")) || null)

// Computed initials
const initials = computed(() => {
  const nameInitial = user.value?.name?.[0] || ""
  const surnameInitial = user.value?.surname?.[0] || ""
  return `${nameInitial}${surnameInitial}`.toUpperCase() || "U"
})

// Logout function
function logoutUser() {
  localStorage.removeItem("token")
  localStorage.removeItem("user")
  router.replace("/login")
}

const search = ref("")

const friends = ref([
  { id: 1, name: "Anna Kalniņa", initials: "AK", color: "secondary", books: 10 },
  { id: 2, name: "Pēteris Mārtins", initials: "PM", color: "teal", books: 7 },
  { id: 3, name: "Līga Vītola", initials: "LV", color: "orange", books: 5 },
])

const suggestions = ref([
  { id: 4, name: "Māris R.", initials: "MR", color: "pink", mutual: 8 },
  { id: 5, name: "Elīna L.", initials: "EL", color: "indigo", mutual: 5 },
])

const filteredFriends = computed(() => {
  return friends.value.filter((f) =>
    f.name.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>

<style scoped>
.friends-page {
  min-height: 100vh;
  background: rgb(var(--v-theme-background));
}

.page-content {
  background: rgb(var(--v-theme-background));
}

.app-header {
  background: rgb(var(--v-theme-surface)) !important;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.panel-card,
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.search-field :deep(.v-field) {
  background: rgba(var(--v-theme-on-surface), 0.05);
}
</style>