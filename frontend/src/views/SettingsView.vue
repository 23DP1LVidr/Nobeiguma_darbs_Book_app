<template>
  <v-app>
    <v-main>
      <v-container class="py-10" style="max-width: 600px;">
        <v-card rounded="xl" elevation="2" class="pa-8">
          <div class="text-h5 font-weight-bold mb-6">Profila iestatījumi</div>
          <v-divider class="mb-6"></v-divider>
          <!-- Theme Switch -->
          <div class="mb-6">
            <div class="text-subtitle-1 font-weight-bold mb-2">Izskats</div>
            <v-btn-toggle v-model="theme" mandatory class="mb-2">
              <v-btn value="light" prepend-icon="mdi-white-balance-sunny">Gaišs</v-btn>
              <v-btn value="dark" prepend-icon="mdi-moon-waning-crescent">Tumšs</v-btn>
            </v-btn-toggle>
          </div>
          <v-divider class="mb-6"></v-divider>
          <!-- Privacy -->
          <div class="mb-6">
            <div class="text-subtitle-1 font-weight-bold mb-2">Privātums</div>
            <v-select
              v-model="privacy"
              :items="privacyOptions"
              label="Kas var redzēt jūsu grāmatas?"
              prepend-icon="mdi-eye"
            ></v-select>
          </div>
          <v-divider class="mb-6"></v-divider>
          <!-- Save Button -->
          <div class="d-flex justify-end">
            <v-btn color="primary" @click="saveSettings">Saglabāt izmaiņas</v-btn>
          </div>
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, watch } from 'vue'
const theme = ref('light')
const privacy = ref('Visi')
const privacyOptions = ['Visi', 'Tikai draugi', 'Tikai es']

watch(theme, (val) => {
  if (val === 'dark') {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
})

function saveSettings() {
  alert('Iestatījumi saglabāti!')
}
</script>
