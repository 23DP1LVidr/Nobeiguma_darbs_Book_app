<template>
  <div class="settings-page">
    <AppHeader :user="user" :initials="initials" />

    <v-container fluid class="pa-0 page-content">
      <v-container class="py-6" style="max-width: 1200px;">
        <v-row>
          <!-- Sidebar -->
          <v-col cols="12" md="3" class="d-none d-md-block">
            <AppSidebar :user="user" :initials="initials" @logout="logoutUser" />
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
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"

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