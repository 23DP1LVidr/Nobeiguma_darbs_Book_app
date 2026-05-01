<template>
  <v-container fluid class="hero-section pa-0">
    <!-- Navigation Bar -->
    <v-app-bar flat color="transparent" class="px-8">
      <v-toolbar-title class="text-h5 font-weight-bold text-primary">BookSwap</v-toolbar-title>
      
      <v-spacer></v-spacer>
      
      <v-btn variant="text" class="mr-2" href="/home">Sākums</v-btn>
      <v-btn variant="text" class="mr-2" href="/about">Par mums</v-btn>
      <v-btn variant="text" class="mr-4" href="/register">Reģistrēties</v-btn>
      <v-btn color="primary" variant="flat" href="/register">Sākt</v-btn>
    </v-app-bar>

    <!-- Hero Section -->
    <v-container class="py-16">
      <v-row justify="center" align="center">
        <v-col cols="12" md="10" class="text-center">
          
          <v-chip color="primary" variant="tonal" class="mb-6" size="small">
            📚 Grāmatu apmaiņas platforma #1 Latvijā
          </v-chip>

          <h1 class="hero-title mb-6">
            Atklāj jaunas grāmatas,<br>
            <span class="text-primary">satiec lasītājus</span>
          </h1>

          <p class="hero-subtitle mb-8">
            BookSwap ir moderna sociālā platforma grāmatu lasītājiem. 
            Maini grāmatas, sazinies ar citiem lasītājiem un veido savu 
            personīgo bibliotēku — viss vienā vietā.
          </p>

          <div class="d-flex justify-center gap-4 flex-wrap">
            <v-btn color="primary" size="large" elevation="4" href="/register">
              <v-icon start>mdi-account-plus</v-icon>
              Reģistrēties bezmaksas
            </v-btn>
            <v-btn variant="outlined" size="large" href="/about" color="primary">
              <v-icon start>mdi-information</v-icon>
              Uzzināt vairāk
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <!-- Stats Section -->
      <v-row justify="center" class="mt-12">
        <v-col cols="6" sm="3" class="text-center">
          <div class="stat-number">500+</div>
          <div class="stat-label">Grāmatas</div>
        </v-col>
        <v-col cols="6" sm="3" class="text-center">
          <div class="stat-number">120+</div>
          <div class="stat-label">Lietotāji</div>
        </v-col>
        <v-col cols="6" sm="3" class="text-center">
          <div class="stat-number">50+</div>
          <div class="stat-label">Apmainītas</div>
        </v-col>
        <v-col cols="6" sm="3" class="text-center">
          <div class="stat-number">10</div>
          <div class="stat-label">Pilsētas</div>
        </v-col>
      </v-row>
    </v-container>

    <!-- Features Section -->
    <v-container class="py-16 features-section">
      <v-row justify="center">
        <v-col cols="12" class="text-center mb-12">
          <h2 class="section-title mb-4">Kāpēc izmantot BookSwap?</h2>
          <p class="section-text">Viss, kas nepieciešams grāmatu apmaiņai, vienuviet</p>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="4">
          <v-card class="feature-card pa-6" elevation="2" rounded="xl" height="100%">
            <v-avatar color="primary" variant="tonal" size="60" class="mb-4">
              <v-icon size="30" color="primary">mdi-bookshelf</v-icon>
            </v-avatar>
            <h3 class="text-h6 font-weight-bold mb-2">Personīgā bibliotēka</h3>
            <p class="text-body-2 text-medium-emphasis">
              Pievieno visas savas grāmatas digitālajā bibliotēkā. 
              Seko līdzi savam krājumam jebkurā laikā.
            </p>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card class="feature-card pa-6" elevation="2" rounded="xl" height="100%">
            <v-avatar color="secondary" variant="tonal" size="60" class="mb-4">
              <v-icon size="30" color="secondary">mdi-swap-horizontal</v-icon>
            </v-avatar>
            <h3 class="text-h6 font-weight-bold mb-2">Vienkārša apmaiņa</h3>
            <p class="text-body-2 text-medium-emphasis">
              Atrodi citus lasītājus savā apkārtnē un apmaini grāmatas 
              tikai ar dažiem klikšķiem.
            </p>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card class="feature-card pa-6" elevation="2" rounded="xl" height="100%">
            <v-avatar color="tertiary" variant="tonal" size="60" class="mb-4">
              <v-icon size="30" color="tertiary">mdi-account-group</v-icon>
            </v-avatar>
            <h3 class="text-h6 font-weight-bold mb-2">Lasītāju kopiena</h3>
            <p class="text-body-2 text-medium-emphasis">
              Sazinies ar citiem grāmatmīļiem, dalies ar iespaidiem 
              un atklāj jaunas grāmatas kopā.
            </p>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- CTA Section -->
    <v-container class="py-16 cta-section">
      <v-row justify="center">
        <v-col cols="12" md="8" class="text-center">
          <h2 class="cta-title mb-4">Gatavs sākt?</h2>
          <p class="cta-text mb-8">
            Pievieno savas grāmatas jau šodien un sāc apmainīties ar citiem lasītājiem visā Latvijā.
          </p>
          <v-btn color="white" size="x-large" elevation="8" href="/register" class="text-primary">
            <v-icon start>mdi-rocket-launch</v-icon>
            Reģistrēties tagad
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <!-- Footer -->
    <v-footer color="grey-darken-4" class="py-8">
      <v-container>
        <v-row>
          <v-col cols="12" md="4">
            <div class="text-h6 font-weight-bold text-primary mb-2">BookSwap</div>
            <p class="text-body-2 text-grey">Moderna platforma grāmatu apmaiņai Latvijā.</p>
          </v-col>
          <v-col cols="6" md="4">
            <div class="text-subtitle-2 font-weight-bold mb-2">Saite</div>
            <div class="d-flex flex-column">
              <a href="/home" class="text-grey text-decoration-none">Sākums</a>
              <a href="/about" class="text-grey text-decoration-none">Par mums</a>
              <a href="/register" class="text-grey text-decoration-none">Reģistrācija</a>
            </div>
          </v-col>
          <v-col cols="6" md="4">
            <div class="text-subtitle-2 font-weight-bold mb-2">Kontakts</div>
            <p class="text-grey text-body-2">info@bookswap.lv</p>
          </v-col>
        </v-row>
        <v-divider class="my-4"></v-divider>
        <div class="text-center text-grey">
          © 2026 BookSwap. Visas tiesības rezervētas.
        </div>
      </v-container>
    </v-footer>
  </v-container>
</template>

<script setup>
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  min-height: 100vh;
}

.hero-title {
  font-size: clamp(36px, 6vw, 64px);
  font-weight: 800;
  letter-spacing: -1px;
  line-height: 1.1;
}

.hero-subtitle {
  font-size: clamp(16px, 2vw, 20px);
  color: #6e6e73;
  max-width: 700px;
  margin: 0 auto;
  line-height: 1.6;
}

.stat-number {
  font-size: 36px;
  font-weight: 800;
  color: #1976d2;
}

.stat-label {
  font-size: 14px;
  color: #6e6e73;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.section-title {
  font-size: clamp(28px, 4vw, 36px);
  font-weight: 700;
}

.section-text {
  font-size: 18px;
  color: #6e6e73;
}

.feature-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
}

.features-section {
  background: white;
}

.cta-section {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
}

.cta-title {
  font-size: 36px;
  font-weight: 700;
  color: white;
}

.cta-text {
  font-size: 18px;
  color: rgba(255,255,255,0.9);
}

.gap-4 {
  gap: 16px;
}
</style>
