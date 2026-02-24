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
            required
          />
  
          <v-text-field
            v-model="email"
            label="E-pasts"
            type="email"
            variant="outlined"
            class="mb-3"
            required
          />
  
          <v-text-field
            v-model="password"
            label="Parole"
            type="password"
            variant="outlined"
            class="mb-3"
            required
          />
  
          <v-text-field
            v-model="passwordConfirm"
            label="Atkārtot paroli"
            type="password"
            variant="outlined"
            class="mb-6"
            required
          />
  
          <v-btn type="submit" color="primary" size="large" block>
            Reģistrēties
          </v-btn>
  
          <div class="text-center mt-4">
            <span class="text-medium-emphasis">Jau ir konts?</span>
            <router-link to="/login" class="ml-1">Pieteikties</router-link>
          </div>
        </v-form>
      </v-card>
    </v-container>
  </template>
  
  <script setup>
  import { ref } from "vue";
  
  const name = ref("");
  const email = ref("");
  const password = ref("");
  const passwordConfirm = ref("");
  
  function onSubmit() {
    if (password.value !== passwordConfirm.value) {
      alert("Paroles nesakrīt!");
      return;
    }
    alert("Reģistrācija (demo): " + email.value);
  }
  </script>