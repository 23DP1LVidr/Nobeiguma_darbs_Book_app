<template>
  <v-app>
    <!-- Top Navigation Bar -->
    <v-app-bar color="white" elevation="1" class="px-4">
      <div class="d-flex align-center" style="width: 280px;">
        <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
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
      <v-btn icon variant="text" class="mr-1">
        <v-icon>mdi-home</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1">
        <v-icon>mdi-bookswap</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1">
        <v-icon>mdi-bell-outline</v-icon>
      </v-btn>
      
      <!-- User Avatar -->
      <router-link to="/profile">
        <v-avatar size="36" color="primary" class="ml-2" style="cursor:pointer;">
          <span class="text-white text-caption">JD</span>
        </v-avatar>
      </router-link>
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
                  <v-list-item prepend-icon="mdi-book-open-variant" title="Mana bibliotēka" value="library" href="/library"></v-list-item>
                  <v-list-item prepend-icon="mdi-swap-horizontal" title="Manas apmaiņas" value="swaps" href="/exchanges"></v-list-item>
                  <v-list-item prepend-icon="mdi-cog" title="Iestatījumi" value="settings" href="/settings"></v-list-item>
                  <v-list-item prepend-icon="mdi-message-text" title="Ziņas" value="messages"></v-list-item>
                  <v-list-item prepend-icon="mdi-account-group" title="Draugi" value="friends"></v-list-item>
                </v-list>
              </v-card>

              <!-- Your Books Preview -->
              <v-card rounded="xl" elevation="0" class="pa-4" color="transparent">
                <div class="text-subtitle-2 font-weight-bold mb-3">Jūsu grāmatas</div>
                <div class="d-flex gap-2 flex-wrap">
                  <v-chip size="small" color="primary" variant="tonal">Fantāzija (4)</v-chip>
                  <v-chip size="small" color="secondary" variant="tonal">Detektīvs (3)</v-chip>
                  <v-chip size="small" color="tertiary" variant="tonal">Zinātne (2)</v-chip>
                </div>
              </v-card>
            </v-col>

            <!-- Main Content - Two Tabs -->
            <v-col cols="12" md="6">
              <!-- Tab Navigation -->
              <v-tabs v-model="activeTab" color="primary" align-tabs="start" class="mb-4">
                <v-tab value="feed" class="text-none">
                  <v-icon start>mdi-newspaper</v-icon>
                  Plūsma
                </v-tab>
                <v-tab value="exchange" class="text-none">
                  <v-icon start>mdi-bookswap</v-icon>
                  Apmaiņa
                </v-tab>
              </v-tabs>

              <!-- Tab Content -->
              <v-window v-model="activeTab">
                <!-- FEED TAB -->
                <v-window-item value="feed">
                  <!-- Create Post Card -->
                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4">
                    <div class="d-flex align-center">
                      <v-avatar size="40" color="primary" class="mr-3">
                        <span class="text-white">JD</span>
                      </v-avatar>
                      <v-text-field
                        v-model="newPost"
                        placeholder="Ko tu domā par grāmatām?"
                        variant="outlined"
                        density="compact"
                        hide-details
                        class="flex-grow-1"
                        bg-color="grey-lighten-4"
                      ></v-text-field>
                    </div>
                    <div class="d-flex justify-end mt-3 gap-2">
                      <v-btn variant="tonal" size="small" prepend-icon="mdi-image">Bilde</v-btn>
                      <v-btn color="primary" size="small">Publicēt</v-btn>
                    </div>
                  </v-card>

                  <!-- Feed Posts -->
                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4">
                    <div class="d-flex align-center mb-3">
                      <v-avatar size="40" color="secondary" class="mr-3">
                        <span class="text-white">AK</span>
                      </v-avatar>
                      <div>
                        <div class="text-subtitle-2 font-weight-bold">Anna Kalniņa</div>
                        <div class="text-caption text-medium-emphasis">Pirms 2 stundām</div>
                      </div>
                    </div>
                    <p class="text-body-1 mb-3">
                      Tikko pabeidzu lasīt "Staburadzi" — brīnišķīga grāmata! 
                      Autentisks stāsts par Latvijas dabu un cilvēkiem. Iesaku visiem!
                    </p>
                    <v-img
                      src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=600"
                      aspect-ratio="1.8"
                      class="rounded-lg mb-3"
                      cover
                    ></v-img>
                    <div class="d-flex gap-4">
                      <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">Patīk</v-btn>
                      <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">Komentēt</v-btn>
                      <v-btn variant="text" size="small" prepend-icon="mdi-share-variant">Kopīgot</v-btn>
                    </div>
                  </v-card>

                  <!-- Book Review Post -->
                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4">
                    <div class="d-flex align-center mb-3">
                      <v-avatar size="40" color="teal" class="mr-3">
                        <span class="text-white">PM</span>
                      </v-avatar>
                      <div>
                        <div class="text-subtitle-2 font-weight-bold">Pēteris Mārtins</div>
                        <div class="text-caption text-medium-emphasis">Pirms 5 stundām</div>
                      </div>
                    </div>
                    <div class="d-flex mb-3">
                      <v-img
                        src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=200"
                        width="80"
                        height="120"
                        class="rounded-lg mr-4"
                        cover
                      ></v-img>
                      <div>
                        <div class="text-subtitle-1 font-weight-bold">1984</div>
                        <div class="text-body-2 text-medium-emphasis">Džordžs Orvels</div>
                        <v-rating :model-value="5" color="amber" density="compact" readonly size="small"></v-rating>
                      </div>
                    </div>
                    <p class="text-body-1">
                      Otrā reize, kad lasu šo klasiku. Katru reizi atrodu ko jaunu. 
                      Grāmata par brīvību, varu un patiesību — aktuāla arī mūsdienās.
                    </p>
                    <div class="d-flex gap-4 mt-3">
                      <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">Patīk (24)</v-btn>
                      <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">Komentēt (8)</v-btn>
                    </div>
                  </v-card>

                  <!-- Simple Post -->
                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4">
                    <div class="d-flex align-center mb-3">
                      <v-avatar size="40" color="orange" class="mr-3">
                        <span class="text-white">LV</span>
                      </v-avatar>
                      <div>
                        <div class="text-subtitle-2 font-weight-bold">Līga Vītola</div>
                        <div class="text-caption text-medium-emphasis">Vakar</div>
                      </div>
                    </div>
                    <p class="text-body-1 mb-3">
                      Meklēju kādu, kurš var iemainīt man "Mērnieks" pret citu latviešu klasiku. 
                      Interesē arī jaunākas grāmatas!
                    </p>
                    <div class="d-flex gap-4">
                      <v-btn variant="text" size="small" prepend-icon="mdi-heart-outline">Patīk (12)</v-btn>
                      <v-btn variant="text" size="small" prepend-icon="mdi-comment-outline">Atbildēt (3)</v-btn>
                    </div>
                  </v-card>
                </v-window-item>

                <!-- EXCHANGE TAB -->
                <v-window-item value="exchange">
                  <!-- Search & Filter Bar -->
                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4">
                    <v-row align="center">
                      <v-col cols="12" sm="6">
                        <v-text-field
                          v-model="bookSearch"
                          placeholder="Meklēt grāmatas..."
                          prepend-inner-icon="mdi-magnify"
                          variant="outlined"
                          density="compact"
                          hide-details
                          bg-color="grey-lighten-4"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6" sm="3">
                        <v-select
                          v-model="genreFilter"
                          :items="genres"
                          label="Žanrs"
                          variant="outlined"
                          density="compact"
                          hide-details
                        ></v-select>
                      </v-col>
                      <v-col cols="6" sm="3">
                        <v-select
                          v-model="conditionFilter"
                          :items="conditions"
                          label="Stāvoklis"
                          variant="outlined"
                          density="compact"
                          hide-details
                        ></v-select>
                      </v-col>
                    </v-row>
                  </v-card>

                  <!-- Available Books Grid -->
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-card rounded="xl" elevation="2" class="pa-4">
                        <div class="d-flex">
                          <v-img
                            src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=150"
                            width="100"
                            height="150"
                            class="rounded-lg mr-4"
                            cover
                          ></v-img>
                          <div class="flex-grow-1">
                            <div class="text-subtitle-1 font-weight-bold">Pērkoņa zirgs</div>
                            <div class="text-body-2 text-medium-emphasis mb-2">Ainažu Frics</div>
                            <v-chip size="x-small" color="primary" variant="tonal" class="mr-1">Fantāzija</v-chip>
                            <v-chip size="x-small" color="success" variant="tonal">Labs</v-chip>
                            <div class="text-caption text-medium-emphasis mt-2">
                              <v-icon size="small">mdi-map-marker</v-icon>
                              Rīga, Centrs
                            </div>
                          </div>
                        </div>
                        <v-divider class="my-3"></v-divider>
                        <div class="d-flex justify-space-between align-center">
                          <div class="d-flex align-center">
                            <v-avatar size="24" color="secondary" class="mr-2">
                              <span class="text-white text-caption">AK</span>
                            </v-avatar>
                            <span class="text-caption">Anna K.</span>
                          </div>
                          <v-btn size="small" color="primary" variant="tonal">Apmainīties</v-btn>
                        </div>
                      </v-card>
                    </v-col>

                    <v-col cols="12" sm="6">
                      <v-card rounded="xl" elevation="2" class="pa-4">
                        <div class="d-flex">
                          <v-img
                            src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=150"
                            width="100"
                            height="150"
                            class="rounded-lg mr-4"
                            cover
                          ></v-img>
                          <div class="flex-grow-1">
                            <div class="text-subtitle-1 font-weight-bold">Melu anatomija</div>
                            <div class="text-body-2 text-medium-emphasis mb-2">Māra Bērziņa</div>
                            <v-chip size="x-small" color="primary" variant="tonal" class="mr-1">Romāns</v-chip>
                            <v-chip size="x-small" color="warning" variant="tonal">Vidējs</v-chip>
                            <div class="text-caption text-medium-emphasis mt-2">
                              <v-icon size="small">mdi-map-marker</v-icon>
                              Jelgava
                            </div>
                          </div>
                        </div>
                        <v-divider class="my-3"></v-divider>
                        <div class="d-flex justify-space-between align-center">
                          <div class="d-flex align-center">
                            <v-avatar size="24" color="teal" class="mr-2">
                              <span class="text-white text-caption">PM</span>
                            </v-avatar>
                            <span class="text-caption">Pēteris M.</span>
                          </div>
                          <v-btn size="small" color="primary" variant="tonal">Apmainīties</v-btn>
                        </div>
                      </v-card>
                    </v-col>

                    <v-col cols="12" sm="6">
                      <v-card rounded="xl" elevation="2" class="pa-4">
                        <div class="d-flex">
                          <v-img
                            src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150"
                            width="100"
                            height="150"
                            class="rounded-lg mr-4"
                            cover
                          ></v-img>
                          <div class="flex-grow-1">
                            <div class="text-subtitle-1 font-weight-bold">Čempionu brokastis</div>
                            <div class="text-body-2 text-medium-emphasis mb-2">Džefs Kinels</div>
                            <v-chip size="x-small" color="primary" variant="tonal" class="mr-1">Pašpalīdzība</v-chip>
                            <v-chip size="x-small" color="success" variant="tonal">Lielisks</v-chip>
                            <div class="text-caption text-medium-emphasis mt-2">
                              <v-icon size="small">mdi-map-marker</v-icon>
                              Liepāja
                            </div>
                          </div>
                        </div>
                        <v-divider class="my-3"></v-divider>
                        <div class="d-flex justify-space-between align-center">
                          <div class="d-flex align-center">
                            <v-avatar size="24" color="orange" class="mr-2">
                              <span class="text-white text-caption">LV</span>
                            </v-avatar>
                            <span class="text-caption">Līga V.</span>
                          </div>
                          <v-btn size="small" color="primary" variant="tonal">Apmainīties</v-btn>
                        </div>
                      </v-card>
                    </v-col>

                    <v-col cols="12" sm="6">
                      <v-card rounded="xl" elevation="2" class="pa-4">
                        <div class="d-flex">
                          <v-img
                            src="https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=150"
                            width="100"
                            height="150"
                            class="rounded-lg mr-4"
                            cover
                          ></v-img>
                          <div class="flex-grow-1">
                            <div class="text-subtitle-1 font-weight-bold">Prāts bez robežām</div>
                            <div class="text-body-2 text-medium-emphasis mb-2">Deivids Dočs</div>
                            <v-chip size="x-small" color="primary" variant="tonal" class="mr-1">Zinātne</v-chip>
                            <v-chip size="x-small" color="success" variant="tonal">Labs</v-chip>
                            <div class="text-caption text-medium-emphasis mt-2">
                              <v-icon size="small">mdi-map-marker</v-icon>
                              Daugavpils
                            </div>
                          </div>
                        </div>
                        <v-divider class="my-3"></v-divider>
                        <div class="d-flex justify-space-between align-center">
                          <div class="d-flex align-center">
                            <v-avatar size="24" color="purple" class="mr-2">
                              <span class="text-white text-caption">ZK</span>
                            </v-avatar>
                            <span class="text-caption">Zane K.</span>
                          </div>
                          <v-btn size="small" color="primary" variant="tonal">Apmainīties</v-btn>
                        </div>
                      </v-card>
                    </v-col>
                  </v-row>

                  <!-- Load More -->
                  <div class="text-center mt-6">
                    <v-btn variant="outlined" color="primary">Ielādēt vairāk grāmatu</v-btn>
                  </div>
                </v-window-item>
              </v-window>
            </v-col>

            <!-- Right Sidebar -->
            <v-col cols="12" md="3" class="d-none d-md-block">
              <!-- Trending Books -->
              <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="transparent">
                <div class="text-subtitle-1 font-weight-bold mb-3">Populāras šonedēļ</div>
                
                <div class="d-flex align-center mb-3">
                  <v-img
                    src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=80"
                    width="50"
                    height="70"
                    class="rounded-lg mr-3"
                    cover
                  ></v-img>
                  <div>
                    <div class="text-body-2 font-weight-bold">Staburadis</div>
                    <div class="text-caption text-medium-emphasis">12 meklējumi</div>
                  </div>
                </div>
                
                <div class="d-flex align-center mb-3">
                  <v-img
                    src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=80"
                    width="50"
                    height="70"
                    class="rounded-lg mr-3"
                    cover
                  ></v-img>
                  <div>
                    <div class="text-body-2 font-weight-bold">1984</div>
                    <div class="text-caption text-medium-emphasis">8 meklējumi</div>
                  </div>
                </div>

                <div class="d-flex align-center">
                  <v-img
                    src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=80"
                    width="50"
                    height="70"
                    class="rounded-lg mr-3"
                    cover
                  ></v-img>
                  <div>
                    <div class="text-body-2 font-weight-bold">Mērnieks</div>
                    <div class="text-caption text-medium-emphasis">6 meklējumi</div>
                  </div>
                </div>
              </v-card>

              <!-- Suggested Users -->
              <v-card rounded="xl" elevation="0" class="pa-4" color="transparent">
                <div class="text-subtitle-1 font-weight-bold mb-3">Ieteiktie draugi</div>
                
                <div class="d-flex align-center justify-space-between mb-3">
                  <div class="d-flex align-center">
                    <v-avatar size="36" color="pink" class="mr-3">
                      <span class="text-white">MR</span>
                    </v-avatar>
                    <div>
                      <div class="text-body-2 font-weight-bold">Māris R.</div>
                      <div class="text-caption text-medium-emphasis">8 kopīgi draugi</div>
                    </div>
                  </div>
                  <v-btn size="small" variant="text" color="primary">Pievienot</v-btn>
                </div>

                <div class="d-flex align-center justify-space-between mb-3">
                  <div class="d-flex align-center">
                    <v-avatar size="36" color="indigo" class="mr-3">
                      <span class="text-white">EL</span>
                    </v-avatar>
                    <div>
                      <div class="text-body-2 font-weight-bold">Elīna L.</div>
                      <div class="text-caption text-medium-emphasis">5 kopīgi draugi</div>
                    </div>
                  </div>
                  <v-btn size="small" variant="text" color="primary">Pievienot</v-btn>
                </div>

                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-avatar size="36" color="cyan" class="mr-3">
                      <span class="text-white">JR</span>
                    </v-avatar>
                    <div>
                      <div class="text-body-2 font-weight-bold">Jūlija R.</div>
                      <div class="text-caption text-medium-emphasis">3 kopīgi draugi</div>
                    </div>
                  </div>
                  <v-btn size="small" variant="text" color="primary">Pievienot</v-btn>
                </div>
              </v-card>
            </v-col>
          </v-row>

        </v-container>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'

// Navigation
const activeTab = ref('feed')
const globalSearch = ref('')

// Feed
const newPost = ref('')

// Exchange
const bookSearch = ref('')
const genreFilter = ref(null)
const conditionFilter = ref(null)

const genres = ['Visi', 'Fantāzija', 'Romāns', 'Detektīvs', 'Zinātne', 'Pašpalīdzība', 'Dzeja']
const conditions = ['Visi', 'Lielisks', 'Labs', 'Vidējs', 'Labs']
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
.gap-4 {
  gap: 16px;
}
</style>