<template>
  <div class="home-page">
    <v-app-bar flat height="72" color="transparent" class="home-nav px-4 px-md-8">
      <router-link to="/home" class="brand-link">
        <div class="brand-mark">B</div>
        <span class="brand-text">BookSwap</span>
      </router-link>

      <v-spacer />

      <div class="d-none d-md-flex align-center nav-links">
        <v-btn variant="text" to="/home">Sākums</v-btn>
        <v-btn variant="text" to="/main">Plūsma</v-btn>
      </div>

      <v-btn variant="text" to="/login" class="ml-2">Pieteikties</v-btn>
      <v-btn color="primary" variant="flat" to="/register" class="ml-2">
        Sākt
      </v-btn>
    </v-app-bar>

    <main>
      <section class="hero-section">
        <v-container class="hero-container">
          <v-row align="center" class="hero-grid">
            <v-col cols="12" lg="6">
              <v-chip color="primary" variant="tonal" class="mb-5" size="small">
                Grāmatu apmaiņas platforma Latvijā
              </v-chip>

              <h1 class="hero-title">
                Maini grāmatas. Atklāj lasītājus. Veido savu bibliotēku.
              </h1>

              <p class="hero-subtitle">
                BookSwap apvieno personīgo bibliotēku, grāmatu apmaiņu,
                sociālo plūsmu un ziņas vienā ērtā vietā.
              </p>

              <div class="hero-actions">
                <v-btn color="primary" size="large" to="/register">
                  <v-icon start>mdi-account-plus</v-icon>
                  Reģistrēties bez maksas
                </v-btn>

                <v-btn variant="outlined" color="primary" size="large" to="/main">
                  <v-icon start>mdi-newspaper</v-icon>
                  Apskatīt plūsmu
                </v-btn>
              </div>

              <div class="trust-row">
                <div>
                  <strong>500+</strong>
                  <span>grāmatas</span>
                </div>
                <div>
                  <strong>120+</strong>
                  <span>lasītāji</span>
                </div>
                <div>
                  <strong>50+</strong>
                  <span>apmaiņas</span>
                </div>
              </div>
            </v-col>

            <v-col cols="12" lg="6">
              <div class="product-preview">
                <div class="preview-topbar">
                  <div class="preview-dot" />
                  <div class="preview-dot" />
                  <div class="preview-dot" />
                  <span>BookSwap</span>
                </div>

                <div class="preview-layout">
                  <aside class="preview-sidebar">
                    <div class="preview-user">
                      <div class="preview-avatar">AK</div>
                      <div>
                        <div class="preview-name">Anna Kalniņa</div>
                        <div class="preview-muted">@annalasa</div>
                      </div>
                    </div>
                    <div class="preview-nav active">Sākums</div>
                    <div class="preview-nav">Mana bibliotēka</div>
                    <div class="preview-nav">Ziņas</div>
                  </aside>

                  <section class="preview-feed">
                    <div class="preview-card">
                      <div class="preview-card-title">1984</div>
                      <div class="preview-muted">Džordžs Orvels</div>
                      <p>
                        Meklēju maiņu pret latviešu klasiku. Grāmata labā stāvoklī.
                      </p>
                      <div class="preview-tags">
                        <span>Distopija</span>
                        <span>Labā stāvoklī</span>
                      </div>
                    </div>

                    <div class="preview-card compact">
                      <div>
                        <div class="preview-card-title">Staburags</div>
                        <div class="preview-muted">12 meklējumi šonedēļ</div>
                      </div>
                      <v-icon color="primary">mdi-trending-up</v-icon>
                    </div>
                  </section>
                </div>
              </div>
            </v-col>
          </v-row>
        </v-container>
      </section>

      <section class="features-section">
        <v-container>
          <div class="section-heading">
            <h2>Viss grāmatu apmaiņai vienā sistēmā</h2>
            <p>BookSwap nav tikai katalogs. Tā ir darba vide lasītājiem.</p>
          </div>

          <v-row>
            <v-col v-for="feature in features" :key="feature.title" cols="12" md="4">
              <div class="feature-card">
                <div class="feature-icon">
                  <v-icon>{{ feature.icon }}</v-icon>
                </div>
                <h3>{{ feature.title }}</h3>
                <p>{{ feature.text }}</p>
              </div>
            </v-col>
          </v-row>
        </v-container>
      </section>

      <section class="steps-section">
        <v-container>
          <v-row align="center">
            <v-col cols="12" md="5">
              <h2 class="section-title">Kā tas strādā</h2>
              <p class="section-copy">
                Process ir vienkāršs: pievieno savas grāmatas, atrodi citu
                piedāvājumus un vienojies par apmaiņu ziņās.
              </p>
            </v-col>

            <v-col cols="12" md="7">
              <div class="steps-list">
                <div v-for="step in steps" :key="step.title" class="step-item">
                  <div class="step-number">{{ step.number }}</div>
                  <div>
                    <h3>{{ step.title }}</h3>
                    <p>{{ step.text }}</p>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>
        </v-container>
      </section>

      <section class="cta-section">
        <v-container>
          <div class="cta-panel">
            <div>
              <h2>Sāc mainīt grāmatas jau šodien</h2>
              <p>Pievieno pirmās grāmatas un atrodi lasītājus savā pilsētā.</p>
            </div>
            <v-btn color="primary" size="large" to="/register">
              Izveidot kontu
            </v-btn>
          </div>
        </v-container>
      </section>
    </main>

    <v-footer class="home-footer">
      <v-container class="d-flex flex-column flex-md-row align-center justify-space-between">
        <div class="font-weight-bold">BookSwap</div>
        <div class="text-body-2 text-medium-emphasis">
          © 2026 BookSwap. Visas tiesības rezervētas.
        </div>
      </v-container>
    </v-footer>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted } from "vue"
import { useTheme } from "vuetify"

const theme = useTheme()
let previousTheme = "light"

const features = [
  {
    icon: "mdi-bookshelf",
    title: "Personīgā bibliotēka",
    text: "Pievieno grāmatas, norādi žanru, stāvokli un attēlu. Viss pārskatāmi vienā vietā.",
  },
  {
    icon: "mdi-swap-horizontal",
    title: "Droša apmaiņa",
    text: "Sūti apmaiņas pieprasījumus un seko statusam no gaidoša līdz pabeigtam.",
  },
  {
    icon: "mdi-message-text",
    title: "Ziņas un kopiena",
    text: "Vienojies par detaļām, komentē ierakstus un atrodi aktīvus lasītājus.",
  },
]

const steps = [
  {
    number: "01",
    title: "Izveido profilu",
    text: "Reģistrējies un papildini profilu ar pilsētu un īsu aprakstu.",
  },
  {
    number: "02",
    title: "Pievieno grāmatas",
    text: "Norādi, kuras grāmatas esi gatavs mainīt ar citiem lietotājiem.",
  },
  {
    number: "03",
    title: "Sāc apmaiņu",
    text: "Atrodi interesējošu grāmatu un nosūti apmaiņas pieprasījumu.",
  },
]

onMounted(() => {
  previousTheme = theme.global.name.value
  theme.global.name.value = "light"
})

onBeforeUnmount(() => {
  theme.global.name.value = previousTheme
})
</script>

<style scoped>
.home-page {
  min-height: 100vh;
  color: #172033;
  background: #f6f8fb;
}

.home-nav {
  border-bottom: 1px solid rgba(23, 32, 51, 0.08);
  backdrop-filter: blur(18px);
}

.brand-link {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: inherit;
  text-decoration: none;
}

.brand-mark {
  display: grid;
  width: 36px;
  height: 36px;
  color: #ffffff;
  font-weight: 800;
  place-items: center;
  border-radius: 10px;
  background: #1976d2;
}

.brand-text {
  color: #172033;
  font-size: 20px;
  font-weight: 800;
}

.nav-links {
  gap: 4px;
}

.hero-section {
  min-height: 760px;
  padding-top: 72px;
  background:
    radial-gradient(circle at 80% 10%, rgba(25, 118, 210, 0.16), transparent 34%),
    linear-gradient(180deg, #ffffff 0%, #f6f8fb 100%);
}

.hero-container {
  padding-top: 72px;
  padding-bottom: 72px;
}

.hero-grid {
  min-height: 560px;
}

.hero-title {
  max-width: 720px;
  margin: 0;
  color: #101828;
  font-size: clamp(42px, 6vw, 72px);
  font-weight: 900;
  letter-spacing: 0;
  line-height: 1.02;
}

.hero-subtitle,
.section-copy {
  max-width: 620px;
  margin-top: 24px;
  color: #596579;
  font-size: 19px;
  line-height: 1.7;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 34px;
}

.trust-row {
  display: flex;
  flex-wrap: wrap;
  gap: 28px;
  margin-top: 42px;
}

.trust-row div {
  display: grid;
  gap: 2px;
}

.trust-row strong {
  color: #101828;
  font-size: 28px;
}

.trust-row span {
  color: #667085;
  font-size: 14px;
}

.product-preview {
  overflow: hidden;
  border: 1px solid rgba(23, 32, 51, 0.1);
  border-radius: 24px;
  background: #ffffff;
  box-shadow: 0 28px 80px rgba(16, 24, 40, 0.14);
}

.preview-topbar {
  display: flex;
  align-items: center;
  gap: 8px;
  height: 52px;
  padding: 0 18px;
  color: #667085;
  border-bottom: 1px solid rgba(23, 32, 51, 0.08);
}

.preview-topbar span {
  margin-left: 10px;
  font-weight: 700;
}

.preview-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #d0d5dd;
}

.preview-layout {
  display: grid;
  grid-template-columns: 190px 1fr;
  min-height: 420px;
}

.preview-sidebar {
  padding: 20px;
  background: #f8fafc;
  border-right: 1px solid rgba(23, 32, 51, 0.08);
}

.preview-user {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 24px;
}

.preview-avatar {
  display: grid;
  width: 40px;
  height: 40px;
  color: #ffffff;
  font-size: 13px;
  font-weight: 800;
  place-items: center;
  border-radius: 50%;
  background: #1976d2;
}

.preview-name,
.preview-card-title {
  color: #101828;
  font-weight: 800;
}

.preview-muted {
  color: #667085;
  font-size: 13px;
}

.preview-nav {
  padding: 10px 12px;
  color: #475467;
  border-radius: 10px;
  font-size: 14px;
}

.preview-nav.active {
  color: #101828;
  background: #e8f2ff;
}

.preview-feed {
  display: grid;
  align-content: start;
  gap: 16px;
  padding: 24px;
}

.preview-card {
  padding: 20px;
  border: 1px solid rgba(23, 32, 51, 0.08);
  border-radius: 18px;
  background: #ffffff;
}

.preview-card.compact {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.preview-card p {
  color: #475467;
  line-height: 1.6;
}

.preview-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.preview-tags span {
  padding: 6px 10px;
  color: #175cd3;
  border-radius: 999px;
  background: #eff8ff;
  font-size: 13px;
  font-weight: 700;
}

.features-section,
.steps-section,
.cta-section {
  padding: 90px 0;
  background: #ffffff;
}

.steps-section {
  background: #f6f8fb;
}

.section-heading {
  max-width: 720px;
  margin: 0 auto 42px;
  text-align: center;
}

.section-heading h2,
.section-title,
.cta-panel h2 {
  color: #101828;
  font-size: clamp(30px, 4vw, 44px);
  font-weight: 900;
  letter-spacing: 0;
}

.section-heading p,
.cta-panel p {
  margin-top: 12px;
  color: #667085;
  font-size: 18px;
}

.feature-card {
  height: 100%;
  padding: 28px;
  border: 1px solid rgba(23, 32, 51, 0.08);
  border-radius: 22px;
  background: #ffffff;
  box-shadow: 0 12px 30px rgba(16, 24, 40, 0.06);
}

.feature-icon {
  display: grid;
  width: 54px;
  height: 54px;
  margin-bottom: 20px;
  color: #1976d2;
  place-items: center;
  border-radius: 16px;
  background: #e8f2ff;
}

.feature-card h3,
.step-item h3 {
  margin-bottom: 10px;
  color: #101828;
  font-size: 20px;
  font-weight: 800;
}

.feature-card p,
.step-item p {
  color: #667085;
  line-height: 1.65;
}

.steps-list {
  display: grid;
  gap: 16px;
}

.step-item {
  display: grid;
  grid-template-columns: 72px 1fr;
  gap: 18px;
  padding: 24px;
  border: 1px solid rgba(23, 32, 51, 0.08);
  border-radius: 20px;
  background: #ffffff;
}

.step-number {
  color: #1976d2;
  font-size: 24px;
  font-weight: 900;
}

.cta-section {
  padding-top: 40px;
}

.cta-panel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  padding: 36px;
  border-radius: 24px;
  background: #eef6ff;
}

.home-footer {
  padding: 28px 0;
  border-top: 1px solid rgba(23, 32, 51, 0.08);
  background: #ffffff;
}

@media (max-width: 959px) {
  .hero-section {
    min-height: auto;
  }

  .hero-container {
    padding-top: 48px;
  }

  .preview-layout {
    grid-template-columns: 1fr;
  }

  .preview-sidebar {
    display: none;
  }

  .cta-panel {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>
