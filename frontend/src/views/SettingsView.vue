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
            <v-card rounded="xl" elevation="0" class="pa-6 content-card">
              <div class="text-h5 font-weight-bold mb-6">Iestatījumi</div>

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

              <div class="mb-6">
                <div class="text-subtitle-1 font-weight-bold mb-2">Konts</div>

                <v-expansion-panels variant="accordion" class="account-panels">
                  <v-expansion-panel elevation="0" class="account-panel">
                    <v-expansion-panel-title>
                      <div class="d-flex align-center">
                        <v-icon class="mr-3">mdi-lock-reset</v-icon>
                        <span>Mainīt paroli</span>
                      </div>
                    </v-expansion-panel-title>

                    <v-expansion-panel-text>
                      <v-text-field
                        v-model="passwordForm.current_password"
                        label="Pašreizējā parole"
                        :type="showCurrentPassword ? 'text' : 'password'"
                        :append-inner-icon="showCurrentPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        :error-messages="passwordErrors.current_password"
                        autocomplete="current-password"
                        @click:append-inner="showCurrentPassword = !showCurrentPassword"
                      />

                      <v-text-field
                        v-model="passwordForm.password"
                        label="Jaunā parole"
                        :type="showNewPassword ? 'text' : 'password'"
                        :append-inner-icon="showNewPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        :error-messages="passwordErrors.password"
                        autocomplete="new-password"
                        @click:append-inner="showNewPassword = !showNewPassword"
                      />

                      <v-text-field
                        v-model="passwordForm.password_confirmation"
                        label="Atkārtot jauno paroli"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        :append-inner-icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        :error-messages="passwordErrors.password_confirmation"
                        autocomplete="new-password"
                        @click:append-inner="showConfirmPassword = !showConfirmPassword"
                      />

                      <div class="d-flex justify-end">
                        <v-btn
                          color="primary"
                          variant="tonal"
                          :loading="changingPassword"
                          @click="changePassword"
                        >
                          Saglabāt paroli
                        </v-btn>
                      </div>
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>

                <v-btn
                  class="mt-4"
                  color="error"
                  variant="tonal"
                  prepend-icon="mdi-logout"
                  @click="showLogoutConfirm = true"
                >
                  Izrakstīties
                </v-btn>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="3" class="d-none d-md-block locked-page__side">
            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Padomi</div>
              <div class="text-body-2 text-medium-emphasis">
                Vari mainīt tēmu un konta paroli jebkurā laikā.
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>

    <v-snackbar v-model="showSnackbar" :color="snackbarColor" timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>

    <v-dialog v-model="showLogoutConfirm" max-width="420">
      <v-card rounded="lg" class="content-card">
        <v-card-title class="text-h6 font-weight-bold">
          Izrakstīties?
        </v-card-title>

        <v-card-text>
          Vai tiešām vēlies izrakstīties no sava konta?
        </v-card-text>

        <v-card-actions>
          <v-spacer />

          <v-btn variant="text" @click="showLogoutConfirm = false">
            Atcelt
          </v-btn>

          <v-btn color="error" variant="tonal" @click="logoutUser">
            Izrakstīties
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "vue"
import axios from "axios"
import { useTheme } from "vuetify"
import { useRouter } from "vue-router"
import { API_URL } from "@/services/api"
import { usePageLock } from "@/composables/usePageLock"
import AppHeader from "@/components/layout/AppHeader.vue"
import AppSidebar from "@/components/layout/AppSidebar.vue"
import { logout } from "@/stores/userStore"

const router = useRouter()
const vuetifyTheme = useTheme()

usePageLock()

const user = ref(JSON.parse(localStorage.getItem("user")) || null)
const selectedTheme = ref("light")

const passwordForm = ref({
  current_password: "",
  password: "",
  password_confirmation: "",
})
const passwordErrors = ref({})
const changingPassword = ref(false)
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const showSnackbar = ref(false)
const snackbarMessage = ref("")
const snackbarColor = ref("success")
const showLogoutConfirm = ref(false)

const initials = computed(() => {
  const nameInitial = user.value?.name?.[0] || ""
  const surnameInitial = user.value?.surname?.[0] || ""
  return `${nameInitial}${surnameInitial}`.toUpperCase() || "U"
})

onMounted(() => {
  const savedTheme = localStorage.getItem("theme") || "light"
  selectedTheme.value = savedTheme
  vuetifyTheme.global.name.value = savedTheme
})

watch(selectedTheme, (newTheme) => {
  vuetifyTheme.global.name.value = newTheme
  localStorage.setItem("theme", newTheme)
})

function logoutUser() {
  logout(router)
}

async function changePassword() {
  passwordErrors.value = {}

  if (!passwordForm.value.current_password) {
    passwordErrors.value.current_password = "Pašreizējā parole ir obligāta."
  }

  if (!passwordForm.value.password) {
    passwordErrors.value.password = "Jaunā parole ir obligāta."
  } else if (passwordForm.value.password.length < 6) {
    passwordErrors.value.password = "Parolei jābūt vismaz 6 simboliem."
  }

  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    passwordErrors.value.password_confirmation = "Paroles nesakrīt."
  }

  if (Object.keys(passwordErrors.value).length) return

  changingPassword.value = true

  try {
    await axios.put(`${API_URL}/password`, passwordForm.value, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    })

    passwordForm.value = {
      current_password: "",
      password: "",
      password_confirmation: "",
    }
    showMessage("Parole atjaunināta.", "success")
  } catch (error) {
    passwordErrors.value = error.response?.data?.errors || {}
    showMessage(error.response?.data?.message || "Neizdevās atjaunināt paroli.", "error")
  } finally {
    changingPassword.value = false
  }
}

function showMessage(message, color = "success") {
  snackbarMessage.value = message
  snackbarColor.value = color
  showSnackbar.value = true
}
</script>

<style scoped>
.panel-card,
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.account-panels {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  border-radius: 12px;
  overflow: hidden;
}

.account-panel {
  background: rgba(var(--v-theme-on-surface), 0.03) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
}
</style>
