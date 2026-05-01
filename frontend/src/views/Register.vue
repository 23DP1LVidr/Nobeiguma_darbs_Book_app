<template>
    <v-container class="py-16" style="max-width: 520px;">
      <h1 class="text-h4 font-weight-bold mb-2">Reģistrācija</h1>
      <p class="text-body-1 text-medium-emphasis mb-8">
        Izveido kontu, lai pievienotu grāmatas un apmainītos ar citiem lasītājiem.
      </p>
  
      <v-card class="pa-6" rounded="xl" elevation="10">
        <v-form @submit.prevent="onSubmit">
          <v-text-field
            v-model="name"
            label="Vārds"
            variant="outlined"
            class="mb-3"
            :error-messages="nameError"
            @blur="touched.name = true"
            required
          />

          <v-text-field
            v-model="surname"
            label="Uzvārds"
            variant="outlined"
            class="mb-3"
            :error-messages="surnameError"
            @blur="touched.surname = true"
            required
          />

          <v-text-field
            v-model="username"
            label="Lietotājvārds"
            variant="outlined"
            class="mb-3"
            :error-messages="usernameError"
            @blur="touched.username = true"
            hint="Piemērs: janisberzins"
          />

          <v-text-field
            v-model="email"
            label="E-pasts"
            type="email"
            variant="outlined"
            class="mb-3"
            :error-messages="emailError"
            @blur="touched.email = true"
            required
          />

          <v-text-field
            v-model="password"
            label="Parole"
            :type="showPassword ? 'text' : 'password'"
            variant="outlined"
            class="mb-3"
            :error-messages="passwordError"
            @blur="touched.password = true"
            :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append-inner="showPassword = !showPassword"
            required
          />

          <v-text-field
            v-model="passwordConfirm"
            label="Atkārtot paroli"
            :type="showPasswordConfirm ? 'text' : 'password'"
            variant="outlined"
            class="mb-6"
            :error-messages="passwordConfirmError"
            @blur="touched.passwordConfirm = true"
            :append-inner-icon="showPasswordConfirm ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
            required
          />

          <v-btn
            type="submit"
            color="primary"
            size="large"
            block
            :loading="loading"
            :disabled="!formIsValid"
          >
            Reģistrēties
          </v-btn>
        </v-form>
      </v-card>
    </v-container>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from "vue"
  import { useRouter } from "vue-router"
  import axios from "axios"

  const router = useRouter()

  const name = ref("")
  const surname = ref("")
  const username = ref("")
  const email = ref("")
  const password = ref("")
  const passwordConfirm = ref("")
  const loading = ref(false)

  const showPassword = ref(false)
  const showPasswordConfirm = ref(false)

  const touched = reactive({
    name: false,
    surname: false,
    username: false,
    email: false,
    password: false,
    passwordConfirm: false,
  })

  const nameError = computed(() => {
    if (!touched.name) return ""
    if (!name.value.trim()) return "Vārds ir obligāts."
    if (name.value.length < 2) return "Vārdam jābūt vismaz 2 simboliem."
    return ""
  })

  const surnameError = computed(() => {
    if (!touched.surname) return ""
    if (!surname.value.trim()) return "Uzvārds ir obligāts."
    if (surname.value.length < 2) return "Uzvārdam jābūt vismaz 2 simboliem."
    return ""
  })

  const usernameError = computed(() => {
    if (!touched.username) return ""
    if (!username.value.trim()) return "Lietotājvārds ir obligāts."
    if (username.value.length < 4) return "Lietotājvārdam jābūt vismaz 4 simboliem."
    if (!/^[a-z0-9._]+$/.test(username.value)) {
      return "Atļauti tikai mazie burti, cipari, punkti un apakšsvītras."
    }
    return ""
  })

  const emailError = computed(() => {
    if (!touched.email) return ""
    if (!email.value.trim()) return "E-pasts ir obligāts."
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
      return "Ievadi derīgu e-pastu."
    }
    return ""
  })

  const passwordError = computed(() => {
    if (!touched.password) return ""
    if (!password.value) return "Parole ir obligāta."
    if (password.value.length < 6) return "Parolei jābūt vismaz 6 simboliem."
    return ""
  })

  const passwordConfirmError = computed(() => {
    if (!touched.passwordConfirm) return ""
    if (!passwordConfirm.value) return "Atkārtota parole ir obligāta."
    if (passwordConfirm.value !== password.value) return "Paroles nesakrīt."
    return ""
  })

  const formIsValid = computed(() => {
    return (
      name.value.trim() &&
      surname.value.trim() &&
      username.value.trim() &&
      email.value.trim() &&
      password.value &&
      passwordConfirm.value &&
      !nameError.value &&
      !surnameError.value &&
      !usernameError.value &&
      !emailError.value &&
      !passwordError.value &&
      !passwordConfirmError.value
    )
  })

  function touchAll() {
    Object.keys(touched).forEach((key) => {
      touched[key] = true
    })
  }

  async function onSubmit() {
    touchAll()

    if (!formIsValid.value) return

    loading.value = true

    try {
      const response = await axios.post("http://127.0.0.1:8000/api/register", {
        name: name.value,
        surname: surname.value,
        username: username.value,
        email: email.value,
        password: password.value,
      })

      localStorage.setItem("token", response.data.token)
      localStorage.setItem("user", JSON.stringify(response.data.user))

      router.push("/main")
    } catch (error) {
      if (error.response?.status === 422) {
        const errors = error.response.data.errors

        if (errors?.email) alert(errors.email[0])
        else if (errors?.username) alert(errors.username[0])
        else alert("Pārbaudi ievadītos datus.")
      } else {
        alert("Reģistrācija neizdevās.")
      }
    } finally {
      loading.value = false
    }
  }
  </script>