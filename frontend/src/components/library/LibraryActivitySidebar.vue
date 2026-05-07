<template>
  <v-col cols="12" md="3" class="d-none d-md-block">
    <v-card rounded="xl" elevation="0" class="pa-4 panel-card">
      <div class="text-subtitle-1 font-weight-bold mb-3">Bibliotēkas statistika</div>

      <div class="stat-row mb-3">
        <v-icon color="primary" class="mr-3">mdi-book-open-variant</v-icon>
        <div>
          <div class="text-caption text-medium-emphasis">Grāmatu skaits</div>
          <div class="text-h6 font-weight-bold">{{ books.length }}</div>
        </div>
      </div>

      <v-divider class="my-3" />

      <div class="stat-row">
        <v-icon color="secondary" class="mr-3">mdi-tag</v-icon>
        <div>
          <div class="text-caption text-medium-emphasis">Populārākais žanrs</div>
          <div class="text-body-1 font-weight-bold">
            {{ topGenreLabel }}
          </div>
        </div>
      </div>
    </v-card>
  </v-col>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  books: {
    type: Array,
    default: () => [],
  },
})

const topGenreLabel = computed(() => {
  const counts = props.books.reduce((result, book) => {
    const genre = book.genre || "Bez žanra"
    result[genre] = (result[genre] || 0) + 1
    return result
  }, {})

  const topGenre = Object.entries(counts).sort((a, b) => b[1] - a[1])[0]

  if (!topGenre) return "Nav grāmatu"

  return `${topGenre[0]} (${topGenre[1]})`
})
</script>

<style scoped>
.panel-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.stat-row {
  display: flex;
  align-items: center;
}
</style>
