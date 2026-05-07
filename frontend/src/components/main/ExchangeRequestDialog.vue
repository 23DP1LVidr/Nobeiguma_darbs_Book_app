<template>
  <v-dialog :model-value="modelValue" max-width="520" @update:model-value="emitClose">
    <v-card rounded="xl" class="pa-6 content-card">
      <div class="text-h6 font-weight-bold mb-4">Pieprasīt apmaiņu</div>

      <div class="exchange-preview mb-4">
        <div v-if="selectedBook" class="exchange-preview-item">
          <div class="text-body-2 text-medium-emphasis mb-2">
            Jūs vēlaties saņemt:
          </div>

          <div class="d-flex">
            <div class="exchange-cover mr-4">
              <v-img
                v-if="selectedBook.image"
                :src="selectedBook.image"
                width="80"
                height="115"
                class="rounded"
                cover
              />

              <div v-else class="exchange-cover-placeholder rounded">
                <v-icon>mdi-book-open-page-variant</v-icon>
              </div>
            </div>

            <div>
              <div class="text-subtitle-1 font-weight-bold">{{ selectedBook.title }}</div>
              <div class="text-body-2 text-medium-emphasis">{{ selectedBook.author }}</div>
            </div>
          </div>
        </div>

        <v-icon class="exchange-arrow" color="primary">
          mdi-swap-horizontal
        </v-icon>

        <div v-if="selectedOfferedBook" class="exchange-preview-item">
          <div class="text-body-2 text-medium-emphasis mb-2">
            Jūs piedāvājat:
          </div>

          <div class="d-flex">
            <div class="exchange-cover mr-4">
              <v-img
                v-if="selectedOfferedBook.image"
                :src="selectedOfferedBook.image"
                width="80"
                height="115"
                class="rounded"
                cover
              />

              <div v-else class="exchange-cover-placeholder rounded">
                <v-icon>mdi-book-open-page-variant</v-icon>
              </div>
            </div>

            <div>
              <div class="text-subtitle-1 font-weight-bold">{{ selectedOfferedBook.title }}</div>
              <div class="text-body-2 text-medium-emphasis">{{ selectedOfferedBook.author }}</div>
            </div>
          </div>
        </div>
      </div>

      <v-select
        v-model="selectedOfferedBookId"
        :items="myBookOptions"
        label="Izvēlieties grāmatu, ko piedāvāt"
        variant="outlined"
        item-title="title"
        item-value="id"
        class="mb-4"
        clearable
      />

      <div class="d-flex justify-end">
        <v-btn variant="text" class="mr-2" @click="close">
          Atcelt
        </v-btn>

        <v-btn color="primary" :loading="creating" @click="submit">
          Nosūtīt pieprasījumu
        </v-btn>
      </div>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed, ref, watch } from "vue"

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  selectedBook: {
    type: Object,
    default: null,
  },
  myBooks: {
    type: Array,
    default: () => [],
  },
  creating: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(["update:modelValue", "submit"])

const selectedOfferedBookId = ref(null)

const myBookOptions = computed(() => {
  return props.myBooks.map((book) => ({
    id: book.id,
    title: `${book.title} — ${book.author}`,
  }))
})

const selectedOfferedBook = computed(() => {
  return props.myBooks.find((book) => book.id === selectedOfferedBookId.value) || null
})

watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      selectedOfferedBookId.value = null
    }
  }
)

function emitClose(value) {
  emit("update:modelValue", value)
}

function close() {
  emit("update:modelValue", false)
}

function submit() {
  emit("submit", selectedOfferedBookId.value)
}
</script>

<style scoped>
.exchange-preview {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  gap: 16px;
  align-items: center;
}

.exchange-preview-item {
  min-width: 0;
}

.exchange-arrow {
  margin-top: 28px;
}

.exchange-cover {
  width: 80px;
  min-width: 80px;
  height: 115px;
}

.exchange-cover-placeholder {
  width: 80px;
  height: 115px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(var(--v-theme-on-surface), 0.06);
}

.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}
</style>
