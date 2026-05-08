<template>
  <v-container class="py-16" style="max-width: 520px;">
    <h1 class="text-h4 font-weight-bold mb-2">Pieteikšanās</h1>
    <p class="text-body-1 text-medium-emphasis mb-8">
      Pieslēdzies savam kontam, lai piekļūtu grāmatu apmaiņai.
    </p>

    <v-card class="pa-6" rounded="xl" elevation="10">
      <v-form @submit.prevent="onSubmit">

        <v-text-field
          v-model="login"
          label="E-pasts vai lietotājvārds"
          variant="outlined"
          class="mb-3"
          required
        />

        <v-text-field
          v-model="password"
          label="Parole"
          :type="showPassword ? 'text' : 'password'"
          variant="outlined"
          class="mb-4"
          :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="showPassword = !showPassword"
          required
        />

        <div v-if="error" class="text-error mb-3">
          {{ error }}
        </div>

        <v-btn type="submit" color="primary" size="large" block :loading="loading">
          Pieteikties
        </v-btn>

        <div class="text-center mt-4">
          <span class="text-medium-emphasis">Nav konta?</span>
          <router-link to="/register" class="ml-1">
            Reģistrēties
          </router-link>
        </div>

      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import axios from "axios"
import { setLoggedInUser } from "@/stores/userStore"

const router = useRouter()

const login = ref("") // email OR username
const password = ref("")
const loading = ref(false)
const error = ref("")

const showPassword = ref(false)

async function onSubmit() {
  error.value = ""
  loading.value = true

  try {
    const response = await axios.post("http://127.0.0.1:8000/api/login", {
      login: login.value,
      password: password.value,
    })

    localStorage.setItem("token", response.data.token)
    localStorage.setItem("user", JSON.stringify(response.data.user))
    localStorage.setItem("token", response.data.token)
    setLoggedInUser(response.data.user)

    router.replace("/main")
  } catch (err) {
    if (err.response?.status === 422) {
      error.value = "Nepareizs lietotājvārds/e-pasts vai parole"
    } else {
      error.value = "Kļūda pieslēdzoties"
    }
  } finally {
    loading.value = false
  }
}
</script>
