<template>
  <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card library-filter-card">
    <v-text-field
      v-model="bookSearch"
      placeholder="Meklēt pēc nosaukuma vai autora..."
      prepend-inner-icon="mdi-magnify"
      variant="outlined"
      density="compact"
      hide-details
      class="mb-4"
    />

    <v-row>
      <v-col cols="12" sm="8">
        <v-select
          v-model="sortMode"
          :items="sortOptions"
          label="Kārtot pēc"
          variant="outlined"
          density="compact"
          hide-details
        />
      </v-col>

      <v-col cols="12" sm="4">
        <v-btn
          block
          variant="outlined"
          height="40"
          :prepend-icon="sortDirection === 'asc' ? 'mdi-sort-ascending' : 'mdi-sort-descending'"
          @click="toggleSortDirection"
        >
          {{ sortDirection === "asc" ? "A-Z" : "Z-A" }}
        </v-btn>
      </v-col>
    </v-row>
  </v-card>

  <div class="d-flex justify-space-between align-center mb-6">
    <div>
      <h1 class="text-h4 font-weight-bold">Mana bibliotēka</h1>
      <p class="text-body-1 text-medium-emphasis">
        Jūsu personīgais grāmatu krājums
      </p>
    </div>

    <v-btn color="primary" prepend-icon="mdi-plus" @click="$emit('add')">
      Pievienot grāmatu
    </v-btn>
  </div>

  <div v-if="loading" class="text-center py-10">
    <v-progress-circular indeterminate color="primary" />
  </div>

  <div v-else-if="filteredBooks.length === 0" class="text-center py-10">
    <v-icon size="56" color="grey">mdi-book-open-page-variant</v-icon>
    <div class="text-h6 mt-3">Nav atrastu grāmatu</div>
    <div class="text-body-2 text-medium-emphasis mb-4">
      Pievieno grāmatu vai maini meklēšanas/filtra iestatījumus.
    </div>
    <v-btn color="primary" prepend-icon="mdi-plus" @click="$emit('add')">
      Pievienot grāmatu
    </v-btn>
  </div>

  <v-row v-else>
    <v-col
      v-for="book in filteredBooks"
      :key="book.id"
      cols="12"
      sm="6"
      md="4"
    >
      <v-card rounded="xl" elevation="2" class="pa-4 h-100 book-card">
        <div class="d-flex flex-column h-100">
          <v-img
            v-if="book.image"
            :src="book.image"
            aspect-ratio="0.7"
            class="rounded-lg mb-3"
            cover
          />

          <div
            v-else
            class="rounded-lg mb-3 d-flex align-center justify-center"
            style="height: 220px; background: rgba(var(--v-theme-on-surface), 0.06);"
          >
            <v-icon size="48">mdi-book-open-page-variant</v-icon>
          </div>

          <div class="flex-grow-1">
            <div class="text-subtitle-1 font-weight-bold mb-1">
              {{ book.title }}
            </div>

            <div class="text-body-2 text-medium-emphasis mb-2">
              {{ book.author }}
            </div>

            <div class="d-flex gap-2 mb-2 flex-wrap">
              <v-chip v-if="book.genre" size="x-small" color="primary" variant="tonal">
                {{ book.genre }}
              </v-chip>

              <v-chip
                v-if="book.condition"
                size="x-small"
                :color="getConditionColor(book.condition)"
                variant="tonal"
              >
                {{ book.condition }}
              </v-chip>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-auto">
            <v-btn size="small" variant="text" prepend-icon="mdi-pencil" @click="$emit('edit', book)">
              Rediģēt
            </v-btn>

            <v-btn
              size="small"
              variant="text"
              color="error"
              prepend-icon="mdi-delete"
              @click="$emit('delete', book.id)"
            >
              Dzēst
            </v-btn>
          </div>
        </div>
      </v-card>
    </v-col>

    <v-col cols="12" sm="6" md="4">
      <v-card
        rounded="xl"
        elevation="0"
        class="pa-4 h-100 d-flex align-center justify-center add-card"
        @click="$emit('add')"
      >
        <div class="text-center">
          <v-icon size="48" color="grey">mdi-plus</v-icon>
          <div class="text-body-1 text-medium-emphasis mt-2">
            Pievienot grāmatu
          </div>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script setup>
import { computed, ref } from "vue"

const props = defineProps({
  books: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

defineEmits(["add", "edit", "delete"])

const bookSearch = ref("")
const sortMode = ref("alphabetic")
const sortDirection = ref("asc")

const sortOptions = [
  { title: "Alfabētiski", value: "alphabetic" },
  { title: "Pēc stāvokļa", value: "condition" },
]

const conditionOrder = {
  Jauna: 1,
  "Labā stāvoklī": 2,
  "Vidējā stāvoklī": 3,
  "Sliktā stāvoklī": 4,
}

const filteredBooks = computed(() => {
  let result = [...props.books]
  const search = bookSearch.value.trim().toLowerCase()

  if (search) {
    result = result.filter((book) => {
      return (
        book.title?.toLowerCase().includes(search) ||
        book.author?.toLowerCase().includes(search)
      )
    })
  }

  result.sort((a, b) => {
    let comparison = 0

    if (sortMode.value === "alphabetic") {
      comparison = (a.title || "").localeCompare(b.title || "", "lv")
    }

    if (sortMode.value === "condition") {
      comparison =
        (conditionOrder[a.condition] || 999) -
        (conditionOrder[b.condition] || 999)
    }

    return sortDirection.value === "asc" ? comparison : -comparison
  })

  return result
})

function toggleSortDirection() {
  sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc"
}

function getConditionColor(condition) {
  const colors = {
    Jauna: "success",
    "Labā stāvoklī": "primary",
    "Vidējā stāvoklī": "warning",
    "Sliktā stāvoklī": "error",
  }

  return colors[condition] || "grey"
}
</script>

<style scoped>
.book-card,
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.add-card {
  min-height: 300px;
  cursor: pointer;
  background: transparent !important;
  border: 2px dashed rgba(var(--v-theme-on-surface), 0.35);
}

.library-filter-card {
  position: sticky;
  top: 0;
  z-index: 4;
}

.gap-2 {
  gap: 8px;
}
</style>
