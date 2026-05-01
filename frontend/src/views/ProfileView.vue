<template>
  <v-app>
    <v-main>
      <v-container class="py-10" style="max-width: 700px;">
        <v-card rounded="xl" elevation="2" class="pa-8">
          <div class="d-flex align-center mb-6">
            <v-avatar size="96" color="primary" class="mr-6" @click="showAvatarDialog = true" style="cursor:pointer;">
              <v-img :src="profilePicture" v-if="profilePicture" />
              <span v-else class="text-h3 text-white">JD</span>
            </v-avatar>
            <div>
              <div class="text-h5 font-weight-bold mb-1">Jānis Daugulis</div>
              <div class="text-body-2 text-medium-emphasis">@janis.daugulis</div>
              <v-chip color="primary" variant="tonal" class="mt-2">Lasītājs</v-chip>
            </div>
          </div>
          <v-divider class="mb-6"></v-divider>
          <div class="mb-4">
            <div class="text-subtitle-1 font-weight-bold mb-2">Par mani</div>
            <div class="text-body-1">Grāmatu mīļotājs no Rīgas. Patīk lasīt fantāziju, zinātnisko literatūru un dalīties ar citiem lasītājiem.</div>
          </div>
          <v-divider class="mb-6"></v-divider>
          <div class="mb-4">
            <div class="text-subtitle-1 font-weight-bold mb-2">Publiskās grāmatas</div>
            <v-row>
              <v-col cols="12" sm="6" md="4" v-for="book in books" :key="book.id">
                <v-card rounded="xl" elevation="1" class="pa-3">
                  <v-img :src="book.cover" aspect-ratio="0.7" class="rounded-lg mb-2" cover></v-img>
                  <div class="text-subtitle-2 font-weight-bold">{{ book.title }}</div>
                  <div class="text-caption text-medium-emphasis">{{ book.author }}</div>
                </v-card>
              </v-col>
            </v-row>
          </div>
          <v-divider class="mb-6"></v-divider>
          <div class="d-flex justify-end">
            <v-btn color="primary" variant="tonal" href="/settings" prepend-icon="mdi-cog">Rediģēt profilu</v-btn>
          </div>
        </v-card>
      </v-container>
      <!-- Avatar Change Dialog -->
      <v-dialog v-model="showAvatarDialog" max-width="400">
        <v-card class="pa-6">
          <div class="text-h6 font-weight-bold mb-4">Mainīt profila attēlu</div>
          <v-file-input v-model="newAvatar" label="Izvēlieties attēlu" accept="image/*" prepend-icon="mdi-image" show-size></v-file-input>
          <v-btn color="primary" class="mt-4" @click="changeAvatar">Saglabāt</v-btn>
          <v-btn variant="text" class="mt-2" @click="showAvatarDialog = false">Atcelt</v-btn>
        </v-card>
      </v-dialog>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'
const showAvatarDialog = ref(false)
const profilePicture = ref('')
const newAvatar = ref(null)
const books = ref([
  { id: 1, title: 'Pērkoņa zirgs', author: 'Ainažu Frics', cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300' },
  { id: 2, title: '1984', author: 'Džordžs Orvels', cover: 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300' },
  { id: 3, title: 'Melu anatomija', author: 'Māra Bērziņa', cover: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=300' }
])
function changeAvatar() {
  if (newAvatar.value && newAvatar.value.length > 0) {
    const file = newAvatar.value[0]
    const reader = new FileReader()
    reader.onload = e => {
      profilePicture.value = e.target.result
      showAvatarDialog.value = false
    }
    reader.readAsDataURL(file)
  }
}
</script>
