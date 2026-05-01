<template>
  <v-app>
    <!-- Top Navigation Bar -->
    <v-app-bar color="white" elevation="1" class="px-4">
      <div class="d-flex align-center" style="width: 280px;">
        <router-link to="/main" class="text-decoration-none">
          <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
        </router-link>
      </div>

      <!-- Search Bar -->
      <v-text-field
        v-model="globalSearch"
        placeholder="Meklēt grāmatas, lietotājus..."
        prepend-inner-icon="mdi-magnify"
        variant="outlined"
        density="compact"
        hide-details
        single-line
        class="mx-4"
        style="max-width: 400px;"
        bg-color="grey-lighten-4"
      ></v-text-field>

      <v-spacer></v-spacer>

      <!-- Nav Icons -->
      <v-btn icon variant="text" class="mr-1" href="/main">
        <v-icon>mdi-home</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1" href="/library">
        <v-icon>mdi-book-open-variant</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1" href="/exchanges">
        <v-icon>mdi-swap-horizontal</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1">
        <v-icon>mdi-bell-outline</v-icon>
      </v-btn>
      
      <!-- User Avatar -->
      <v-avatar size="36" color="primary" class="ml-2" @click="showUserMenu = !showUserMenu" style="cursor: pointer;">
        <span class="text-white text-caption">JD</span>
      </v-avatar>
    </v-app-bar>

    <v-main>
      <v-container fluid class="bg-grey-lighten-5 pa-0">
        <v-container class="py-6" style="max-width: 1200px;">
          
          <v-row>
            <!-- Left Sidebar -->
            <v-col cols="12" md="3" class="d-none d-md-block">
              <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="transparent">
                <div class="d-flex align-center mb-4">
                  <v-avatar size="48" color="primary" class="mr-3">
                    <span class="text-white">JD</span>
                  </v-avatar>
                  <div>
                    <div class="text-subtitle-1 font-weight-bold">Jānis Daugulis</div>
                    <div class="text-caption text-medium-emphasis">12 grāmatas</div>
                  </div>
                </div>
                <v-divider class="mb-3"></v-divider>
                <v-list density="compact" bg-color="transparent">
                  <v-list-item prepend-icon="mdi-home" title="Sākums" value="home" href="/main"></v-list-item>
                  <v-list-item prepend-icon="mdi-book-open-variant" title="Mana bibliotēka" value="library" href="/library" active-color="primary"></v-list-item>
                  <v-list-item prepend-icon="mdi-swap-horizontal" title="Manas apmaiņas" value="swaps" href="/exchanges"></v-list-item>
                  <v-list-item prepend-icon="mdi-message-text" title="Ziņas" value="messages"></v-list-item>
                  <v-list-item prepend-icon="mdi-account-group" title="Draugi" value="friends"></v-list-item>
                </v-list>
              </v-card>

              <!-- Categories -->
              <v-card rounded="xl" elevation="0" class="pa-4" color="transparent">
                <div class="text-subtitle-2 font-weight-bold mb-3">Kategorijas</div>
                <v-list density="compact" bg-color="transparent">
                  <v-list-item prepend-icon="mdi-star" title="Favorīti" value="favorites"></v-list-item>
                  <v-list-item prepend-icon="mdi-clock-outline" title="Lasīšanā" value="reading"></v-list-item>
                  <v-list-item prepend-icon="mdi-check-all" title="Izlasīts" value="read"></v-list-item>
                  <v-list-item prepend-icon="mdi-swap-horizontal" title="Apmaiņai" value="exchange"></v-list-item>
                </v-list>
              </v-card>
            </v-col>

            <!-- Main Content -->
            <v-col cols="12" md="6">
              <!-- Header -->
              <div class="d-flex justify-space-between align-center mb-6">
                <div>
                  <h1 class="text-h4 font-weight-bold">Mana bibliotēka</h1>
                  <p class="text-body-1 text-medium-emphasis">Jūsu personīgais grāmatu krājums</p>
                </div>
                <v-btn color="primary" prepend-icon="mdi-plus">
                  Pievienot grāmatu
                </v-btn>
              </div>

              <!-- Stats Cards -->

              <!-- Filter Tabs -->
              <v-tabs v-model="activeFilter" color="primary" class="mb-4">
                <v-tab value="all">Visas</v-tab>
                <v-tab value="reading">Lasīšanā</v-tab>
                <v-tab value="read">Izlasīts</v-tab>
                <v-tab value="exchange">Apmaiņai</v-tab>
                <v-tab value="favorites">Favorīti</v-tab>
              </v-tabs>

              <!-- Books Grid -->
              <v-row>
                <v-col cols="12" sm="6" md="4" v-for="book in filteredBooks" :key="book.id">
                  <v-card rounded="xl" elevation="2" class="pa-4 h-100">
                    <div class="d-flex flex-column h-100">
                      <v-img
                        :src="book.cover"
                        aspect-ratio="0.7"
                        class="rounded-lg mb-3"
                        cover
                      ></v-img>
                      <div class="flex-grow-1">
                        <div class="text-subtitle-1 font-weight-bold mb-1">{{ book.title }}</div>
                        <div class="text-body-2 text-medium-emphasis mb-2">{{ book.author }}</div>
                        <div class="d-flex gap-2 mb-2">
                          <v-chip size="x-small" :color="getStatusColor(book.status)">{{ book.status }}</v-chip>
                          <v-chip size="x-small" v-if="book.rating" color="amber" variant="tonal">
                            <v-icon size="x-small" start>mdi-star</v-icon>
                            {{ book.rating }}
                          </v-chip>
                        </div>
                      </div>
                      <div class="d-flex justify-space-between mt-auto">
                        <v-btn size="small" variant="text" prepend-icon="mdi-pencil">Redigēt</v-btn>
                        <v-btn size="small" variant="text" color="primary" prepend-icon="mdi-swap-horizontal">Mainīt</v-btn>
                      </div>
                    </div>
                  </v-card>
                </v-col>
              </v-row>

              <!-- Add Book Card -->
              <v-row>
                <v-col cols="12" sm="6" md="4">
                  <v-card rounded="xl" elevation="0" class="pa-4 h-100 d-flex align-center justify-center" 
                          style="border: 2px dashed grey; min-height: 300px; cursor: pointer;">
                    <div class="text-center">
                      <v-icon size="48" color="grey">mdi-plus</v-icon>
                      <div class="text-body-1 text-medium-emphasis mt-2">Pievienot grāmatu</div>
                    </div>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>

            <!-- Right Sidebar -->
            <v-col cols="12" md="3" class="d-none d-md-block">
              <!-- Recent Activity -->
              <v-card rounded="xl" elevation="0" class="pa-4" color="transparent">
                <div class="text-subtitle-1 font-weight-bold mb-3">Nesenā aktivitāte</div>
                
                <v-timeline density="compact" side="end">
                  <v-timeline-item dot-color="primary" size="x-small">
                    <div class="text-body-2">Pievantoji "Prāts bez robežām"</div>
                    <div class="text-caption text-medium-emphasis">Vakar</div>
                  </v-timeline-item>
                  <v-timeline-item dot-color="success" size="x-small">
                    <div class="text-body-2">Pabeidzi lasīt "Mērnieks"</div>
                    <div class="text-caption text-medium-emphasis">3 dienas atpakaļ</div>
                  </v-timeline-item>
                  <v-timeline-item dot-color="warning" size="x-small">
                    <div class="text-body-2">Ieliki apmaiņai "Čempionu brokastis"</div>
                    <div class="text-caption text-medium-emphasis">Nedēļu atpakaļ</div>
                  </v-timeline-item>
                </v-timeline>
              </v-card>
            </v-col>
          </v-row>

        </v-container>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, computed } from 'vue'

const globalSearch = ref('')
const activeFilter = ref('all')
const showUserMenu = ref(false)

const books = ref([
  {
    id: 1,
    title: 'Pērkoņa zirgs',
    author: 'Ainažu Frics',
    cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300',
    status: 'Lasīšanā',
    rating: 4,
    progress: 65
  },
  {
    id: 2,
    title: '1984',
    author: 'Džordžs Orvels',
    cover: 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300',
    status: 'Lasīšanā',
    rating: 5,
    progress: 30
  },
  {
    id: 3,
    title: 'Melu anatomija',
    author: 'Māra Bērziņa',
    cover: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=300',
    status: 'Lasīšanā',
    rating: null,
    progress: 80
  },
  {
    id: 4,
    title: 'Mērnieks',
    author: 'Kārlis Skalbe',
    cover: 'https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=300',
    status: 'Izlasīts',
    rating: 5,
    progress: 100
  },
  {
    id: 5,
    title: 'Staburadis',
    author: 'Vilis Kadēns',
    cover: 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=300',
    status: 'Izlasīts',
    rating: 4,
    progress: 100
  },
  {
    id: 6,
    title: 'Čempionu brokastis',
    author: 'Džefs Kinels',
    cover: 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=300',
    status: 'Apmaiņai',
    rating: null,
    progress: 0
  },
  {
    id: 7,
    title: 'Prāts bez robežām',
    author: 'Deivids Dočs',
    cover: 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=300',
    status: 'Apmaiņai',
    rating: null,
    progress: 0
  },
  {
    id: 8,
    title: 'Zaļā mile',
    author: 'Ainažu Frics',
    cover: 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=300',
    status: 'Favorīts',
    rating: 5,
    progress: 100
  }
])

const filteredBooks = computed(() => {
  if (activeFilter.value === 'all') return books.value
  return books.value.filter(b => b.status.toLowerCase() === activeFilter.value.toLowerCase())
})

function getStatusColor(status) {
  const colors = {
    'Lasīšanā': 'primary',
    'Izlasīts': 'success',
    'Apmaiņai': 'warning',
    'Favorīts': 'error'
  }
  return colors[status] || 'grey'
}
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>