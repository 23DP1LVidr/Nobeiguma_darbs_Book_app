<template>
  <div class="exchanges-page">
    <!-- Top Navigation Bar -->
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
          <!-- Left Sidebar -->
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

            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-2 font-weight-bold mb-3">Apmaiņu statistika</div>
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Kopā apmainīts</span>
                <span class="text-body-2 font-weight-bold">8</span>
              </div>
              <div class="d-flex justify-space-between mb-2">
                <span class="text-body-2">Gaida apmaiņu</span>
                <span class="text-body-2 font-weight-bold">3</span>
              </div>
              <div class="d-flex justify-space-between">
                <span class="text-body-2">Jūsu piedāvājumi</span>
                <span class="text-body-2 font-weight-bold">2</span>
              </div>
            </v-card>
          </v-col>

          <!-- Main Content -->
          <v-col cols="12" md="6">
            <div class="d-flex justify-space-between align-center mb-6">
              <div>
                <h1 class="text-h4 font-weight-bold">Manas apmaiņas</h1>
                <p class="text-body-1 text-medium-emphasis">Sekojiet līdzi savām grāmatu apmaiņām</p>
              </div>
              <v-btn color="primary" prepend-icon="mdi-plus">
                Jauna apmaiņa
              </v-btn>
            </div>

            <v-tabs v-model="activeTab" color="primary" class="mb-4">
              <v-tab value="pending">Gaida<span class="ml-2">(3)</span></v-tab>
              <v-tab value="active">Aktīvas<span class="ml-2">(2)</span></v-tab>
              <v-tab value="completed">Pabeigtas<span class="ml-2">(8)</span></v-tab>
            </v-tabs>

            <v-window v-model="activeTab">
              <!-- Pending -->
              <v-window-item value="pending">
                <v-card
                  v-for="exchange in pendingExchanges"
                  :key="exchange.id"
                  rounded="xl"
                  elevation="2"
                  class="pa-4 mb-4 content-card"
                >
                  <div class="d-flex justify-space-between align-center mb-3">
                    <div class="d-flex align-center">
                      <v-avatar size="40" :color="exchange.iconColor" class="mr-3">
                        <v-icon color="white">{{ exchange.icon }}</v-icon>
                      </v-avatar>

                      <div>
                        <div class="text-subtitle-1 font-weight-bold">{{ exchange.title }}</div>
                        <div class="text-caption text-medium-emphasis">{{ exchange.subtitle }}</div>
                      </div>
                    </div>

                    <v-chip :color="exchange.chipColor" variant="tonal">{{ exchange.chip }}</v-chip>
                  </div>

                  <v-divider class="mb-3" />

                  <div class="exchange-books">
                    <div class="d-flex align-center">
                      <v-img :src="exchange.leftImage" width="60" height="90" class="rounded-lg mr-3" cover />
                      <div>
                        <div class="text-body-2 font-weight-bold">{{ exchange.leftLabel }}</div>
                        <div class="text-body-1">{{ exchange.leftBook }}</div>
                        <div class="text-caption text-medium-emphasis">{{ exchange.leftAuthor }}</div>
                      </div>
                    </div>

                    <v-icon size="32" color="primary">mdi-arrow-left-right</v-icon>

                    <div class="d-flex align-center">
                      <div class="text-right mr-3">
                        <div class="text-body-2 font-weight-bold">{{ exchange.rightLabel }}</div>
                        <div class="text-body-1">{{ exchange.rightBook }}</div>
                        <div class="text-caption text-medium-emphasis">{{ exchange.rightAuthor }}</div>
                      </div>
                      <v-img :src="exchange.rightImage" width="60" height="90" class="rounded-lg" cover />
                    </div>
                  </div>

                  <v-divider class="my-3" />

                  <div class="d-flex align-center">
                    <v-avatar size="28" :color="exchange.userColor" class="mr-2">
                      <span class="text-white text-caption">{{ exchange.userInitials }}</span>
                    </v-avatar>

                    <span class="text-body-2">{{ exchange.user }}</span>
                    <v-spacer />
                    <span class="text-caption text-medium-emphasis mr-4">{{ exchange.location }}</span>

                    <template v-if="exchange.needsApproval">
                      <v-btn variant="outlined" size="small" class="mr-2">Noraidīt</v-btn>
                      <v-btn color="primary" size="small">Apstiprināt</v-btn>
                    </template>

                    <v-btn v-else variant="text" size="small" color="error">Atcelt</v-btn>
                  </div>
                </v-card>
              </v-window-item>

              <!-- Active -->
              <v-window-item value="active">
                <v-card
                  v-for="exchange in activeExchanges"
                  :key="exchange.id"
                  rounded="xl"
                  elevation="2"
                  class="pa-4 mb-4 content-card"
                >
                  <div class="d-flex justify-space-between align-center mb-3">
                    <div class="d-flex align-center">
                      <v-avatar size="40" :color="exchange.iconColor" class="mr-3">
                        <v-icon color="white">{{ exchange.icon }}</v-icon>
                      </v-avatar>

                      <div>
                        <div class="text-subtitle-1 font-weight-bold">{{ exchange.title }}</div>
                        <div class="text-caption text-medium-emphasis">{{ exchange.subtitle }}</div>
                      </div>
                    </div>

                    <v-chip :color="exchange.chipColor" variant="tonal">{{ exchange.chip }}</v-chip>
                  </div>

                  <v-divider class="mb-3" />

                  <div class="exchange-books">
                    <div class="d-flex align-center">
                      <v-img :src="exchange.leftImage" width="60" height="90" class="rounded-lg mr-3" cover />
                      <div>
                        <div class="text-body-2 font-weight-bold">{{ exchange.leftLabel }}</div>
                        <div class="text-body-1">{{ exchange.leftBook }}</div>
                        <div class="text-caption text-medium-emphasis">{{ exchange.leftAuthor }}</div>
                      </div>
                    </div>

                    <v-icon size="32" :color="exchange.chipColor">mdi-arrow-left-right</v-icon>

                    <div class="d-flex align-center">
                      <div class="text-right mr-3">
                        <div class="text-body-2 font-weight-bold">{{ exchange.rightLabel }}</div>
                        <div class="text-body-1">{{ exchange.rightBook }}</div>
                        <div class="text-caption text-medium-emphasis">{{ exchange.rightAuthor }}</div>
                      </div>
                      <v-img :src="exchange.rightImage" width="60" height="90" class="rounded-lg" cover />
                    </div>
                  </div>

                  <v-divider class="my-3" />

                  <div class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center">
                      <v-avatar size="28" :color="exchange.userColor" class="mr-2">
                        <span class="text-white text-caption">{{ exchange.userInitials }}</span>
                      </v-avatar>

                      <div>
                        <span class="text-body-2">{{ exchange.user }}</span>
                        <div v-if="exchange.meeting" class="text-caption text-primary">{{ exchange.meeting }}</div>
                      </div>
                    </div>

                    <div>
                      <v-btn size="small" variant="text" :prepend-icon="exchange.secondaryIcon">
                        {{ exchange.secondaryAction }}
                      </v-btn>
                      <v-btn size="small" color="primary" variant="tonal">
                        {{ exchange.primaryAction }}
                      </v-btn>
                    </div>
                  </div>
                </v-card>
              </v-window-item>

              <!-- Completed -->
              <v-window-item value="completed">
                <v-card
                  v-for="exchange in completedExchanges"
                  :key="exchange.id"
                  rounded="xl"
                  elevation="0"
                  class="pa-4 mb-4 completed-card"
                >
                  <div class="d-flex justify-space-between align-center">
                    <div class="d-flex align-center">
                      <v-img :src="exchange.image" width="50" height="75" class="rounded-lg mr-3" cover />

                      <div>
                        <div class="text-body-1 font-weight-bold">{{ exchange.book }}</div>
                        <div class="text-caption text-medium-emphasis">pret "{{ exchange.against }}"</div>
                      </div>
                    </div>

                    <div class="text-right">
                      <v-chip color="success" size="small" class="mb-1">Pabeigta</v-chip>
                      <div class="text-caption text-medium-emphasis">{{ exchange.date }}</div>
                    </div>
                  </div>
                </v-card>

                <div class="text-center mt-4">
                  <v-btn variant="text" color="primary">Ielādēt vēl</v-btn>
                </div>
              </v-window-item>
            </v-window>
          </v-col>

          <!-- Right Sidebar -->
          <v-col cols="12" md="3" class="d-none d-md-block">
            <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Ātrās darbības</div>
              <v-btn block variant="tonal" color="primary" class="mb-2" prepend-icon="mdi-plus">Jauna apmaiņa</v-btn>
              <v-btn block variant="outlined" prepend-icon="mdi-magnify">Pārlūkot piedāvājumus</v-btn>
            </v-card>

            <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
              <div class="text-subtitle-1 font-weight-bold mb-3">Nesenās ziņas</div>

              <div v-for="message in recentMessages" :key="message.name" class="d-flex align-center mb-3">
                <v-avatar size="36" :color="message.color" class="mr-3">
                  <span class="text-white">{{ message.initials }}</span>
                </v-avatar>

                <div class="flex-grow-1">
                  <div class="text-body-2 font-weight-bold">{{ message.name }}</div>
                  <div class="text-caption text-medium-emphasis">{{ message.text }}</div>
                </div>

                <div class="text-caption text-medium-emphasis">{{ message.time }}</div>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from "vue"
import { useRouter } from "vue-router"

const router = useRouter()

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

const globalSearch = ref("")
const activeTab = ref("pending")

const pendingExchanges = [
  {
    id: 1,
    icon: "mdi-clock-outline",
    iconColor: "warning",
    title: "Apmaiņas pieprasījums",
    subtitle: "Gaida jūsu apstiprinājumu",
    chip: "Gaida",
    chipColor: "warning",
    leftLabel: "Jūs dodat:",
    leftBook: "Pērkoņa zirgs",
    leftAuthor: "Ainažu Frics",
    leftImage: "https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=150",
    rightLabel: "Jūs saņemat:",
    rightBook: "Melu anatomija",
    rightAuthor: "Māra Bērziņa",
    rightImage: "https://images.unsplash.com/photo-1512820790803-83ca734da794?w=150",
    user: "Anna Kalniņa",
    userInitials: "AK",
    userColor: "secondary",
    location: "Rīga",
    needsApproval: true,
  },
  {
    id: 2,
    icon: "mdi-swap-horizontal",
    iconColor: "info",
    title: "Jūsu piedāvājums",
    subtitle: "Gaida atbildi",
    chip: "Gaida atbildi",
    chipColor: "info",
    leftLabel: "Jūs piedāvājat:",
    leftBook: "1984",
    leftAuthor: "Džordžs Orvels",
    leftImage: "https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150",
    rightLabel: "Pret:",
    rightBook: "Prāts bez robežām",
    rightAuthor: "Deivids Dočs",
    rightImage: "https://images.unsplash.com/photo-1532012197267-da84d127e765?w=150",
    user: "Pēteris Mārtins",
    userInitials: "PM",
    userColor: "teal",
    location: "Liepāja",
    needsApproval: false,
  },
  {
    id: 3,
    icon: "mdi-swap-horizontal",
    iconColor: "info",
    title: "Jūsu piedāvājums",
    subtitle: "Gaida atbildi",
    chip: "Gaida atbildi",
    chipColor: "info",
    leftLabel: "Jūs piedāvājat:",
    leftBook: "Čempionu brokastis",
    leftAuthor: "Džefs Kinels",
    leftImage: "https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=150",
    rightLabel: "Pret:",
    rightBook: "Zaļā mile",
    rightAuthor: "Ainažu Frics",
    rightImage: "https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=150",
    user: "Līga Vītola",
    userInitials: "LV",
    userColor: "orange",
    location: "Jelgava",
    needsApproval: false,
  },
]

const activeExchanges = [
  {
    id: 1,
    icon: "mdi-truck-delivery",
    iconColor: "success",
    title: "Apmaiņa notiek",
    subtitle: "Grāmatas ceļā",
    chip: "Aktīva",
    chipColor: "success",
    leftLabel: "Jūs saņemat:",
    leftBook: "Staburadis",
    leftAuthor: "Vilis Kadēns",
    leftImage: "https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=150",
    rightLabel: "Jūs devāt:",
    rightBook: "Mērnieks",
    rightAuthor: "Kārlis Skalbe",
    rightImage: "https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=150",
    user: "Māris R.",
    userInitials: "MR",
    userColor: "pink",
    secondaryIcon: "mdi-message",
    secondaryAction: "Ziņa",
    primaryAction: "Apstiprināt saņemšanu",
  },
  {
    id: 2,
    icon: "mdi-handshake",
    iconColor: "primary",
    title: "Tikšanās plānota",
    subtitle: "Satiksieties klātienē",
    chip: "Tikšanās",
    chipColor: "primary",
    leftLabel: "Jūs dodat:",
    leftBook: "Melu anatomija",
    leftAuthor: "Māra Bērziņa",
    leftImage: "https://images.unsplash.com/photo-1512820790803-83ca734da794?w=150",
    rightLabel: "Pret:",
    rightBook: "Prāts bez robežām",
    rightAuthor: "Deivids Dočs",
    rightImage: "https://images.unsplash.com/photo-1532012197267-da84d127e765?w=150",
    user: "Elīna L.",
    userInitials: "EL",
    userColor: "indigo",
    meeting: "📍 Rīga, Centrs — 15:00",
    secondaryIcon: "mdi-calendar",
    secondaryAction: "Mainīt",
    primaryAction: "Apstiprināts",
  },
]

const completedExchanges = [
  {
    id: 1,
    book: "Pērkoņa zirgs",
    against: "Zaļā mile",
    date: "2026. gada 15. apr.",
    image: "https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=150",
  },
  {
    id: 2,
    book: "1984",
    against: "Lauva",
    date: "2026. gada 2. apr.",
    image: "https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150",
  },
  {
    id: 3,
    book: "Mērnieks",
    against: "Pīrāgs",
    date: "2026. gada 28. marts",
    image: "https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=150",
  },
  {
    id: 4,
    book: "Zaļā mile",
    against: "Pērkoņa zirgs",
    date: "2026. gada 15. marts",
    image: "https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=150",
  },
  {
    id: 5,
    book: "Čempionu brokastis",
    against: "Veiksmes 7",
    date: "2026. gada 1. marts",
    image: "https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=150",
  },
]

const recentMessages = [
  { name: "Anna Kalniņa", initials: "AK", color: "secondary", text: "Super, tiksimies!", time: "2h" },
  { name: "Pēteris Mārtins", initials: "PM", color: "teal", text: "Vēl varu piedāvāt...", time: "5h" },
  { name: "Līga Vītola", initials: "LV", color: "orange", text: "Paldies par grāmatu!", time: "1d" },
]
</script>

<style scoped>
.exchanges-page {
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
  color: rgb(var(--v-theme-on-surface)) !important;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.panel-card,
.content-card,
.completed-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.completed-card {
  background: rgba(var(--v-theme-on-surface), 0.05) !important;
}

.search-field :deep(.v-field) {
  background: rgba(var(--v-theme-on-surface), 0.05);
}

.exchange-books {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}
</style>