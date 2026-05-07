<template>
  <v-col cols="12" md="3" class="d-none d-md-block">
    <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
      <div class="text-subtitle-1 font-weight-bold mb-3">Ātrās darbības</div>

      <v-btn block variant="tonal" color="primary" class="mb-2" prepend-icon="mdi-magnify" to="/main">
        Grāmatu apmaiņa
      </v-btn>

      <v-btn block variant="outlined" prepend-icon="mdi-book-open-variant" to="/library">
        Mana bibliotēka
      </v-btn>
    </v-card>
  </v-col>
</template>

<style scoped>
.panel-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}
</style>
