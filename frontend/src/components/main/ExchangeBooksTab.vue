<template>
  <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
    <v-row align="center">
      <v-col cols="12" sm="6">
        <v-text-field
          v-model="bookSearch"
          placeholder="Meklēt grāmatas..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          density="compact"
          hide-details
          class="search-field"
        />
      </v-col>

      <v-col cols="6" sm="3">
        <v-select
          v-model="genreFilter"
          :items="genres"
          label="Žanrs"
          variant="outlined"
          density="compact"
          hide-details
        />
      </v-col>

      <v-col cols="6" sm="3">
        <v-select
          v-model="conditionFilter"
          :items="conditions"
          label="Stāvoklis"
          variant="outlined"
          density="compact"
          hide-details
        />
      </v-col>
    </v-row>
  </v-card>

  <div v-if="loading" class="text-center py-10">
    <v-progress-circular indeterminate color="primary" />
  </div>

  <div v-else-if="paginatedExchangeBooks.length === 0" class="text-center py-10">
    <v-icon size="56" color="grey">mdi-book-search</v-icon>
    <div class="text-h6 mt-3">Nav pieejamu grāmatu</div>
  </div>

  <v-row v-else>
    <v-col
      v-for="book in paginatedExchangeBooks"
      :key="book.id"
      cols="12"
      sm="6"
    >
      <v-card rounded="xl" elevation="2" class="pa-4 content-card">
        <div class="d-flex">
          <v-img
            v-if="book.image"
            :src="book.image"
            width="100"
            height="150"
            class="rounded-lg mr-4"
            cover
          />

          <div
            v-else
            class="rounded-lg mr-4 d-flex align-center justify-center"
            style="width: 100px; height: 150px; background: rgba(var(--v-theme-on-surface), 0.06);"
          >
            <v-icon size="40">mdi-book-open-page-variant</v-icon>
          </div>

          <div class="flex-grow-1">
            <div class="text-subtitle-1 font-weight-bold">{{ book.title }}</div>
            <div class="text-body-2 text-medium-emphasis mb-2">{{ book.author }}</div>

            <v-chip v-if="book.genre" size="x-small" color="primary" variant="tonal" class="mr-1">
              {{ book.genre }}
            </v-chip>

            <v-chip v-if="book.condition" size="x-small" :color="getConditionColor(book.condition)" variant="tonal">
              {{ book.condition }}
            </v-chip>
          </div>
        </div>

        <v-divider class="my-3" />

        <div class="d-flex justify-space-between align-center">
          <router-link
            :to="`/profile/${book.user?.id}`"
            class="d-flex align-center owner-info text-decoration-none"
          >
            <v-avatar size="28" color="primary" class="mr-2">
              <v-img v-if="book.user?.avatar" :src="book.user.avatar" />
              <span v-else class="text-white text-caption">
                {{ getUserInitials(book.user) }}
              </span>
            </v-avatar>

            <div>
              <div class="text-caption font-weight-bold text-primary">
                {{ book.user?.name }} {{ book.user?.surname }}
              </div>

              <div class="text-caption text-medium-emphasis">
                {{ book.user?.city || "Pilsēta nav norādīta" }}
              </div>
            </div>
          </router-link>

          <v-btn size="small" color="primary" variant="tonal" @click="$emit('exchange', book)">
            Apmainīties
          </v-btn>
        </div>
      </v-card>
    </v-col>
  </v-row>

  <div v-if="exchangePageCount > 1" class="d-flex justify-center mt-6">
    <v-pagination
      v-model="exchangePage"
      :length="exchangePageCount"
      total-visible="5"
      rounded="circle"
    />
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue"

const props = defineProps({
  books: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  genres: {
    type: Array,
    required: true,
  },
  conditions: {
    type: Array,
    required: true,
  },
})

defineEmits(["exchange"])

const bookSearch = ref("")
const genreFilter = ref("Visi")
const conditionFilter = ref("Visi")
const exchangePage = ref(1)
const booksPerPage = 20

const filteredExchangeBooks = computed(() => {
  let result = [...props.books]
  const search = bookSearch.value.trim().toLowerCase()

  if (search) {
    result = result.filter((book) => {
      return (
        book.title?.toLowerCase().includes(search) ||
        book.author?.toLowerCase().includes(search) ||
        book.genre?.toLowerCase().includes(search)
      )
    })
  }

  if (genreFilter.value !== "Visi") {
    result = result.filter((book) => book.genre === genreFilter.value)
  }

  if (conditionFilter.value !== "Visi") {
    result = result.filter((book) => book.condition === conditionFilter.value)
  }

  return result
})

const exchangePageCount = computed(() => {
  return Math.ceil(filteredExchangeBooks.value.length / booksPerPage)
})

const paginatedExchangeBooks = computed(() => {
  const start = (exchangePage.value - 1) * booksPerPage
  return filteredExchangeBooks.value.slice(start, start + booksPerPage)
})

watch([bookSearch, genreFilter, conditionFilter], () => {
  exchangePage.value = 1
})

function getConditionColor(condition) {
  const colors = {
    Jauna: "success",
    "Labā stāvoklī": "primary",
    "Vidējā stāvoklī": "warning",
    "Sliktā stāvoklī": "error",
  }

  return colors[condition] || "grey"
}

function getUserInitials(user) {
  return `${user?.name?.[0] || ""}${user?.surname?.[0] || ""}`.toUpperCase() || "U"
}
</script>

<style scoped>
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.owner-info {
  min-width: 0;
}

.search-field :deep(.v-field) {
  background: rgba(var(--v-theme-on-surface), 0.05);
}
</style>
