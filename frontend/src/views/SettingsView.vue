<template>
  <div class="settings-page">
    <!-- Top Bar -->
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
          <!-- Sidebar -->
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

          <!-- Main Content -->
          <v-col cols="12" md="6">
            <v-card rounded="xl" elevation="2" class="pa-6 content-card">
              <div class="text-h5 font-weight-bold mb-6">Iestatījumi</div>

              <!-- Theme -->
              <div class="mb-6">
                <div class="text-subtitle-1 font-weight-bold mb-2">Izskats</div>

                <v-btn-toggle v-model="selectedTheme" mandatory>
                  <v-btn value="light" prepend-icon="mdi-white-balance-sunny">
                    Gaišs
                  </v-btn>

                  <v-btn value="dark" prepend-icon="mdi-moon-waning-crescent">
                    Tumšs
                  </v-btn>
                </v-btn-toggle>
              </div>

              <v-divider class="mb-6" />

              <!-- Privacy -->
              <div class="mb-6">
                <div class="text-subtitle-1 font-weight-bold mb-2">Privātums</div>

                <v-select
                  v-model="privacy"
                  :items="privacyOptions"
                  label="Kas var redzēt jūsu grāmatas?"
                />
              </div>

              <v-divider class="mb-6" />

              <div class="d-flex justify-end">
                <v-btn color="primary" @click="saveSettings">
                  Saglabāt izmaiņas
                </v-btn>
              </div>
            </v-card>
          </v-col>

          <!-- Right Side -->
          <v-col cols="12" md="3" class="d-none d-md-block">
            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Padomi</div>
              <div class="text-body-2 text-medium-emphasis">
                Vari mainīt tēmu un privātuma iestatījumus jebkurā laikā.
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from "vue"
import { useTheme } from "vuetify"
import { useRouter } from "vue-router"

const router = useRouter()
const vuetifyTheme = useTheme()

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

const selectedTheme = ref("light")
const privacy = ref("Visi")
const privacyOptions = ["Visi", "Tikai draugi", "Tikai es"]

onMounted(() => {
  const savedTheme = localStorage.getItem("theme") || "light"
  selectedTheme.value = savedTheme
  vuetifyTheme.global.name.value = savedTheme
})

watch(selectedTheme, (newTheme) => {
  vuetifyTheme.global.name.value = newTheme
  localStorage.setItem("theme", newTheme)
})

function saveSettings() {
  localStorage.setItem("privacy", privacy.value)
  alert("Iestatījumi saglabāti!")
}
</script>

<style scoped>
.settings-page {
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
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.panel-card,
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}
</style>